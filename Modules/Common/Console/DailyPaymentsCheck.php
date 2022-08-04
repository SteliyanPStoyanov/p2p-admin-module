<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Entities\Setting;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LogService;
use Modules\Common\Services\TransactionService;
use Modules\Communication\Services\EmailService;

class DailyPaymentsCheck extends CommonCommand
{
    protected $name = 'script:daily-payments-check';
    protected $signature = 'script:daily-payments-check';
    protected $logChannel = 'daily_payments_check';
    protected $description = 'Check transactions and send email';

    protected TransactionService $transactionService;
    protected EmailService $emailService;
    protected InvestorService $investorService;
    protected LogService $logService;

    public function __construct(
        TransactionService $transactionService,
        EmailService $emailService,
        InvestorService $investorService,
        LogService $logService
    ) {
        $this->transactionService = $transactionService;
        $this->emailService = $emailService;
        $this->investorService = $investorService;
        $this->logService = $logService;

        parent::__construct();
    }

    public function handle()
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());
        $today = Carbon::now();

        if ($today->dayOfWeek == Carbon::SUNDAY) {
            return;
        }

        $startOfDay = Carbon::yesterday()->startOfDay()->format('Y-m-d H:i:s');
        $endOfDay = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s');
        $transactionsSum = $this->transactionService->getRepaymentAmountForPeriod($startOfDay, $endOfDay);

        if ($transactionsSum < Calculator::toEuro(Setting::MIN_PAYMENTS_AMOUNT_FOR_ALERT_VALUE)) {
            $env = strtoupper(env('APP_ENV'));
            $subject = 'Low Payment Alerts ('.$env.') - '.Carbon::parse($startOfDay)->format('d-m-Y');
            $body =
                'Payments for date period'.' '.$startOfDay.' '.'-'.' '.$endOfDay.' '.'='.' '.
                $transactionsSum.' '.'EUR.';
            $settlementConfig = config('mail.settlement');

            Mail::raw($body,
                function ($message) use (
                    $subject,
                    $settlementConfig,
                    $body
                ) {
                    $message->subject($subject);
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($settlementConfig['receivers']);
                }
            );
        }

        $log->finish($start);
        $this->log('Finished sending emails for period: '.$startOfDay.'-'.$endOfDay);
        $this->log('Exec.time: '.round((microtime(true) - $start), 2).' second(s)');
        return true;
    }
}
