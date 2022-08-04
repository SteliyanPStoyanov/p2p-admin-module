<?php

namespace App\Http\Controllers;

use App\Http\Request\HomeSearchRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Admin\Entities\BlogPage;
use Modules\Admin\Services\BlogPageService;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\Loan;
use Modules\Common\Events\AffiliateEvents;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\VariablesService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\StorageService;
use ReflectionException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Class HomePageController
 *
 * @package App\Http\Controllers
 */
class HomePageController extends BaseController
{
    /**
     * @var LoanService
     */
    protected LoanService $loanService;
    protected UserAgreementService $userAgreementService;
    protected VariablesService $variableService;
    protected BlogPageService $blogPageService;

    /**
     * HomePageController constructor.
     *
     * @param LoanService $loanService
     * @param UserAgreementService $userAgreementService
     * @param VariablesService $variableService
     * @param BlogPageService $blogPageService
     *
     * @throws ReflectionException
     */
    public function __construct(
        LoanService $loanService,
        UserAgreementService $userAgreementService,
        VariablesService $variableService,
        BlogPageService $blogPageService
    ) {
        $this->loanService = $loanService;
        $this->userAgreementService = $userAgreementService;
        $this->variableService = $variableService;
        $this->blogPageService = $blogPageService;

        parent::__construct();
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        event(new AffiliateEvents(url()->full()));

        $cacheTime = 10 * 60 * 60; //10 hours

        $cachedData = $this->getCachedData(Loan::class);
        if ($cachedData == null) {
            $cachedData = $this->loanService->getLoansForSite(
                5,
                [
                    'active' => 1,
                    'deleted' => 0,
                    'unlisted' => 0,
                ],
                0,
                ['loan_id' => 'DESC']
            );
            $this->setCacheData($cachedData, $cacheTime);
        }

        try {
            return view(
                'pages.home.index',
                [
                    'loans' => $cachedData
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs()
    {
        return view('pages.about.index');
    }

    /**
     * @return Application|Factory|View
     */
    public function howItWorks()
    {
        return view('pages.how-it-works.index');
    }

    /**
     * @return Application|Factory|View
     */
    public function help()
    {
        return view('pages.help.index');
    }

    /**
     * @return Application|Factory|View
     */
    public function affiliate()
    {
        return view('pages.affiliate.index');
    }

    /**
     * @return Application|Factory|View
     */
    public function blog()
    {
        $data = [
            'active' => 1,
            'deleted' => 0
        ];

        $blogPages = $this->blogPageService->getByWhereConditions(BlogPage::LIMIT_BLOG_PAGES, $data);
        $archives = $this->blogPageService->getArchives();

        return view('pages.blog.index', compact('blogPages', 'archives'));
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws Throwable
     */
    public function AjaxBlogPages(Request $request)
    {
        return view(
            'pages.blog.list',
            [
                'blogPages' => $this->blogPageService->getByArchive($request->month),

            ]
        )->render();
    }

    /**
     * @return Application|Factory|View
     */
    public function invest()
    {
        $this->getSessionService()->add($this->cacheKey, []);
        try {
            return view(
                'pages.invest.index',
                [
                    'cacheKey' => $this->cacheKey,
                    'loans' => $this->getTableData(session($this->cacheKey . '.limit')),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function loanOriginators()
    {
        return view('pages.loan-originators.index');
    }


    /**
     * @param HomeSearchRequest $request
     *
     * @return array|string
     * @throws Throwable
     */
    public function refresh(HomeSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'pages.invest.list-table',
            [
                'cacheKey' => $this->cacheKey,
                'loans' => $this->getTableData(session($this->cacheKey . '.limit')),
            ]
        )->render();
    }

    /**
     * @param int|null $limit
     *
     * @return LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        if ($limit != session($this->cacheKey . '.limit')) {
            $data = $this->getSessionService()->get($this->cacheKey);
            $this->getSessionService()->remove($this->cacheKey);
            $data['limit'] = $limit;
            $this->getSessionService()->add($this->cacheKey, $data);
        }

        return $this->loanService->getLoansForSite(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @return Application|Factory|View
     */
    public function template()
    {
        $currentUserAgreement = $this->userAgreementService->getCurrentContractTemplate(
            ContractTemplate::TYPE_INVESTOR
        );

        $content = $currentUserAgreement->text;
        $vars = [
            'Investor' => [
                'investor_id' => '',
            ],
            'Current' => [
                'date' => Carbon::now()->toDateString(),
            ],
            'ContractTemplate' => $currentUserAgreement->toArray(),
        ];
        $content = $this->variableService->replaceVariables($content, $vars);

        return view(
            'pages.user-agreement.index',
            compact('content')
        );
    }

    /**
     * @return StreamedResponse
     * @throws ProblemException
     */
    public function assignmentAgreementTemplate()
    {
        $filePath = StorageService::ASSIGNMENT_AGREEMENT_TEMPLATE_DIR;

        try {
            \Storage::get($filePath);
        } catch (\Throwable $e) {
            $this->userAgreementService->generateAssignmentAgreementTemplate();
        }

        return \Storage::download($filePath);
    }

    /**
     * @return Application|Factory|View
     */
    public function privacyPolicy()
    {
        $content = ($this->userAgreementService->getCurrentContractTemplate(
            ContractTemplate::TYPE_COOKIE_PRIVACY
        ))->text;

        return view(
            'pages.privacy-policy.index',
            compact('content')
        );
    }

    /**
     * @return Application|Factory|View
     */
    public function referAFriend()
    {
        $content = ($this->userAgreementService->getCurrentContractTemplate(
            ContractTemplate::TYPE_REFER_A_FRIEND
        ))->text;

        return view(
            'pages.refer-a-friend.index',
            compact('content')
        );
    }
}
