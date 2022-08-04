<?php

namespace Modules\Profile\Http\Controllers;

use Carbon\Carbon;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\VariablesService;
use Modules\Core\Controllers\BaseController;

class UserAgreementController extends BaseController
{

    /**
     * @var LoanService
     */
    protected LoanService $loanService;
    protected UserAgreementService $userAgreementService;
    protected VariablesService $variableService;

    /**
     * HomePageController constructor.
     *
     * @param LoanService $loanService
     * @param UserAgreementService $userAgreementService
     * @param VariablesService $variableService
     *
     * @throws \ReflectionException
     */
    public function __construct(
        LoanService $loanService,
        UserAgreementService $userAgreementService,
        VariablesService $variableService
    )
    {
        $this->loanService = $loanService;
        $this->userAgreementService = $userAgreementService;
        $this->variableService = $variableService;

        parent::__construct();
    }

    public function index()
    {
        try {
            return view(
                'profile::user-agreement.index',
                [
                    'investor' => $this->getInvestor(),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function template()
    {
        try {
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
                'profile::user-agreement.index',
                compact('content')
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

}
