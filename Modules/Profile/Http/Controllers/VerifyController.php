<?php

namespace Modules\Profile\Http\Controllers;

use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Modules\Common\Entities\Investor;
use Modules\Common\Services\CountryService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\DocumentTypeService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Services\StorageService;
use Modules\Profile\Http\Requests\CompanyDocumentUploadRequest;
use Modules\Profile\Http\Requests\DocumentUploadRequest;
use Modules\Profile\Http\Requests\VerifyRequest;

class VerifyController extends BaseController
{
    protected InvestorService $investorService;
    protected DocumentTypeService $documentTypeService;
    protected CountryService $countryService;
    protected ?StorageService $storageService;

    /**
     * VerifyController constructor.
     *
     * @param InvestorService $investorService
     * @param DocumentTypeService $documentTypeService
     * @param CountryService $countryService
     * @param StorageService $storageService
     *
     * @throws \ReflectionException
     */
    public function __construct(

        InvestorService $investorService,
        DocumentTypeService $documentTypeService,
        CountryService $countryService,
        StorageService $storageService
    ) {
        $this->investorService = $investorService;
        $this->documentTypeService = $documentTypeService;
        $this->countryService = $countryService;
        $this->storageService = $storageService;

        parent::__construct();
    }

    /**
     * @return Application|Factory|View
     */
    public function verify()
    {
        try {
            $countries = $this->countryService->getAll();

            return view(
                'profile::verify.index',
                [
                    'countries' => $countries,
                    'investor' => $this->getInvestor()
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param VerifyRequest $request
     *
     * @return Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function verifySubmit(VerifyRequest $request)
    {
        $validated = $request->validated();

        try {
            $investor = $this->getInvestor();

            // check if already done
            if ($investor->hasActiveVerificationTask()) {
                return redirect()->route('profile.verify.reviewing');
            }

            // check if already verified
            if ($investor->isVerified()) {
                return redirect()->route('profile.dashboard.overview');
            }

            if ($this->investorService->verifyInvestor($validated)) {
                return redirect()->route('profile.verify.uploadPersonalDoc');
            }

            return redirect()->back()->with('fail', __('common.UserRegisterFail'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function uploadPersonalDoc()
    {
        try {
            $documentTypes = $this->documentTypeService->getClientDocumentTypes();

            return view('profile::verify.upload-personal-doc', ['documentTypes' => $documentTypes]);
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param DocumentUploadRequest $request
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function uploadPersonalDocSubmit(DocumentUploadRequest $request)
    {
        $validated = $request->validated();

        try {
            $investor = $this->getInvestor();
            $investorId = $investor->investor_id;

            if (!isset($validated['document_file'])) {
                return redirect()->back();
            }

            if ($validated['document_type_id'] == 1) {
                if (
                    !isset($validated['document_file']['selfie'])
                    || !isset($validated['document_file']['front'])
                    || !isset($validated['document_file']['back'])
                ) {
                    return redirect()->back();
                }
            }

            if ($validated['document_type_id'] == 2) {
                if (
                    !isset($validated['document_file']['selfie'])
                    || !isset($validated['document_file']['front'])
                ) {
                    return redirect()->back();
                }
            }

            // check if already done
            if ($investor->hasActiveVerificationTask()) {
                return redirect()->route('profile.verify.reviewing');
            }

            // check if already verified
            if ($investor->isVerified()) {
                return redirect()->route('profile.dashboard.overview');
            }

            try {
                $saveIdCard = $this->investorService->savePersonalDoc(
                    $investorId,
                    $validated['document_file'],
                    $validated['document_type_id'],
                );
            } catch (\Throwable $exception) {
                return view('errors.generic');
            }

            if (!$saveIdCard) {
                return redirect()->back()->with('fail', __('common.DocumentSaveFail'));
            }

            $this->getCacheService()->remove(config('profile.profileDashboard') . $investorId);

            if ($investor->type == Investor::TYPE_COMPANY) {
                return redirect()->route('profile.verify.company');
            }
            return redirect()->route('profile.verify.reviewing');
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function reviewing()
    {
        try {
            $this->investorService->confirmVerify();

            return view('profile::verify.reviewing');
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return RedirectResponse|View
     */
    public function reviewingSubmit()
    {
        try {
            return redirect()->route('profile.dashboard.overview');
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function company()
    {
        $investor = $this->getInvestor();
        $path = storage_path('pond-upload/' . $investor->investor_id . '/');
        $json = [];

        if (File::exists($path)) {
            $files = File::allFiles($path);


            if ($files) {
                foreach ($files as $file) {
                    $json[] =
                        [
                            'source' => 'pond-upload/' . $investor->investor_id . '/' . $file->getbasename(),
                            'options' => [
                                'type' => 'local',
                                'file' => [
                                    'name' => $file->getbasename(),
                                    'size' => $file->getSize(),
                                    'type' => $file->getExtension(),
                                ]
                            ]
                        ];
                }
            }
        }

        return view('profile::verify.company', ['files' => json_encode($json)]);
    }

    /**
     * @param CompanyDocumentUploadRequest $request
     * @return Application|Factory|RedirectResponse|View
     */
    public function uploadCompanyDoc(CompanyDocumentUploadRequest $request)
    {
        $investor = $this->getInvestor();

        try {
            $saveIdCard = $this->investorService->saveCompanyDocs(
                $investor->investor_id,
                $request['document_file'],
            );
        } catch (\Throwable $exception) {
            return view('errors.generic');
        }

        if (!$saveIdCard) {
            return redirect()->back()->with('fail', __('common.DocumentSaveFail'));
        }

        $this->getCacheService()->remove(config('profile.profileDashboard') . $investor->investor_id);

        return redirect()->route('profile.verify.reviewing');
    }
}
