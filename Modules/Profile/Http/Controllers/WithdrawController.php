<?php

namespace Modules\Profile\Http\Controllers;

use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\TaskService;
use Modules\Communication\Services\EmailService;
use Modules\Core\Controllers\BaseController;
use Modules\Profile\Http\Requests\WithdrawRequest;

class WithdrawController extends BaseController
{

    protected InvestorService $investorService;
    protected EmailService $emailService;
    protected TaskService $taskService;

    public function __construct(
        InvestorService $investorService,
        EmailService $emailService,
        TaskService $taskService
    ) {
        $this->investorService = $investorService;
        $this->emailService = $emailService;
        $this->taskService = $taskService;

        parent::__construct();
    }

    public function index()
    {
        try {
            $investorId = $this->getInvestor()->investor_id;

            return view(
                'profile::withdraw.index',
                [
                    'investor' => $this->getInvestor(),
                    'investorBunch' => $this->taskService->checkInvestorBunch($investorId),
                    'walletSum' => Wallet::sumWalletByInvestor(
                        Currency::ID_EUR,
                        $investorId
                    )
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function withdraw(WithdrawRequest $request)
    {
        $validated = $request->validated();

        try {
            $amount = $validated['amount'];
            $bankAccountId = $validated['bank_account_id'];

            $withdraw = $this->investorService->makeWithdrawTask($amount, $bankAccountId);

            if (!$withdraw) {
                return redirect()->back()->with('fail', __('common.WithdrawFail'));
            }

            return redirect()->back()->with('success', __('common.WithdrawSubmitted'));
        } catch (\Throwable $e) {
            if (
                !empty($e->getCode())
                && $e->getCode() == InvestorService::CODE_ALREADY_SENT_WITHDRAW_REQUEST
            ) {
                return redirect()->back()->with('fail', $e->getMessage());
            }

            return view('errors.generic');
        }
    }
}
