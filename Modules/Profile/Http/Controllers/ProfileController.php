<?php

namespace Modules\Profile\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Common\Entities\Transaction;
use Modules\Common\Exports\AccountStatementExport;
use Modules\Common\Services\CountryService;
use Modules\Common\Services\InvestorContractService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\TransactionService;
use Modules\Common\Services\WalletService;
use Modules\Communication\Services\EmailService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\JsonException;
use Modules\Profile\Http\Requests\InvestmentSearchRequest;
use Modules\Profile\Http\Requests\ProfileTransactionSearchRequest;
use Modules\Profile\Http\Requests\ProfileUpdateRequest;
use Modules\Profile\Http\Requests\SendReferralLinkRequest;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ProfileController extends BaseController
{
    protected CountryService $countryService;
    protected InvestorService $investorService;
    protected EmailService $emailService;
    protected TransactionService $transactionService;
    protected InvestorContractService $investorContractService;
    protected WalletService $walletService;

    public function __construct(
        CountryService $countryService,
        InvestorService $investorService,
        EmailService $emailService,
        InvestorContractService $investorContractService,
        TransactionService $transactionService,
        WalletService $walletService
    ) {
        $this->countryService = $countryService;
        $this->investorService = $investorService;
        $this->emailService = $emailService;
        $this->investorContractService = $investorContractService;
        $this->transactionService = $transactionService;
        $this->walletService = $walletService;

        parent::__construct();
    }

    public function index()
    {
        try {
            return view(
                'profile::profile.index',
                [
                    'investor' => $this->getInvestor(),
                    'countries' => $this->countryService->getAll(),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param ProfileUpdateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(ProfileUpdateRequest $request)
    {
        $validated = $request->validated();

        try {
            if ($request->has('add-funds')) {
                $validated['add-funds'] = true;
            }
            if ($request->has('withdrawal-made')) {
                $validated['withdrawal-made'] = true;
            }
            if ($request->has('new-device')) {
                $validated['new-device'] = true;
            }

            $investor = $this->investorService->update($this->getInvestor(), $validated);

            if ($investor) {
                return redirect()->back()->with('success', __('common.ProfileUpdateSuccess'));
            }
            return redirect()->back()->with('fail', __('common.ProfileUpdateFail'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function referral()
    {
        try {
            $profileHashLink = Auth::user();
            $investor = $this->getInvestor();

            return view(
                'profile::profile.referral',
                compact(
                    'profileHashLink',
                    'investor'
                )
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param SendReferralLinkRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function sendReferralLink(SendReferralLinkRequest $request)
    {
        $validated = $request->validated();

        try {
            $investor = $this->getInvestor();

            $emailExist = $this->emailService->checkReferralEmail($validated['email']);
            if ($emailExist == true) {
                return redirect()->back()->with('fail', __('common.UserEmailExistsRef'));
            }

            $isSend = $this->emailService->sendReferralLink($investor, $validated);
            if ($isSend == true) {
                return redirect()->back()->with('success', __('common.YourReferralLinkSuccess'));
            }

            return redirect()->back()->with('fail', __('common.YourReferralLinkFail'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /****************************************************************/
    /**                        ACCOUNT STATEMENT                   **/
    /****************************************************************/

    public function accountStatement()
    {
        try {
            $this->getSessionService()->add($this->cacheKey, []);
            $key = session($this->cacheKey . '.limit');

            return view(
                'profile::profile.account-statement',
                [
                    'types' => Transaction::getAccountStatementTypes(),
                    'summary' => $this->getTableDataSummary(),
                    'investor' => $this->getInvestor(),
                    'cacheKey' => $this->cacheKey,
                    'transactions' => $this->getTableDataTransactions($key),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param ProfileTransactionSearchRequest $request
     * @return array|string
     * @throws JsonException
     */
    public function refresh(ProfileTransactionSearchRequest $request)
    {
        try {
            parent::setFiltersFromRequest($request);
            $key = session($this->cacheKey . '.limit');

            return view(
                'profile::profile.list-table',
                [
                    'summary' => $this->getTableDataSummary(),
                    'cacheKey' => $this->cacheKey,
                    'transactions' => $this->getTableDataTransactions($key),
                ]
            )->render();
        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param int|null $limit
     * @return LengthAwarePaginator|Collection
     */
    public function getTableDataTransactions(int $limit = null)
    {
        try {
            $investor = $this->getInvestor();

            $request = request();
            $page = !empty($request['page']) ? intval($request['page']) : 1;

            return $this->transactionService->transactionList(
                $investor,
                session($this->cacheKey, []),
                $limit ?? parent::getTableLength(),
                $page
            );
        } catch (\Throwable $e) {
            return new Collection;
        }
    }

    /**
     * @return array
     */
    public function getTableDataSummary(): array
    {
        try {
            $investor = $this->getInvestor();
            $session = session($this->cacheKey, []);

            $today = Carbon::now()->format('Y-m-d');
            $createdAtFrom = isset($session['createdAt']['from']) ? dbDate($session['createdAt']['from']) : $today;
            $createdAtTo = isset($session['createdAt']['to']) ? dbDate($session['createdAt']['to']) : $today;

            $walletBalance = [];
            $walletBalance['start'] = $this->walletService->walletFromDate($investor, $createdAtFrom);
            $walletBalance['end'] = $this->walletService->walletToDate($investor, $createdAtTo);

            $transactions = $this->transactionService->transactionsSum(
                $investor,
                $createdAtFrom,
                $createdAtTo,
                $session['type'] ?? []
            );

            return [
                'walletBalance' => $walletBalance,
                'transaction' => $transactions,
            ];
        } catch (Throwable $e) {
            return [
                'walletBalance' => [],
                'transaction' => [],
            ];
        }
    }

    /**
     * @param ProfileTransactionSearchRequest $request
     * @return BinaryFileResponse
     * @throws JsonException
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(ProfileTransactionSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);
        $investor = $this->getInvestor();

        try {
            $accountStatementExport = new AccountStatementExport(
                $this->transactionService->transactionList(
                    $investor,
                    session($this->cacheKey, [])
                )
            );

            $fileName = $investor->investor_id . '/' . date('Ymd') . '-account-statement-export';
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }

        $this->getStorageService()->download(
            $fileName,
            ['collectionClass' => $accountStatementExport],
            'xlsx',
        );

        return response()->file(storage_path() . '/exports/' . $fileName . '.xlsx');
    }

    /**
     * @param $contractId
     * @return \Illuminate\Contracts\Foundation\Application
     * @return \Illuminate\Contracts\View\Factory
     * @return \Illuminate\View\View
     * @return BinaryFileResponse
     */
    public function agreementDownload($contractId)
    {
        try {
            $investorContract = $this->investorContractService->getById(
                $contractId
            );

            $investorId = (int)Auth::guard('investor')->user()->investor_id;
            if ($investorContract->investor->investor_id != $investorId) {
                abort(403);
            }

            $file = $investorContract->file;

            return response()->file(storage_path() . '/' . $file->file_name);
        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }

}
