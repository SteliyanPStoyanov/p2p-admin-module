<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\TransactionService;
use Throwable;

class RevertInvestorTransactions extends CommonCommand
{
    private $service = null;

    protected $name = 'revert:investor-transactions';
    protected $signature = 'revert:investor-transactions {investorId}';
    protected $description = 'Fix';

    public function __construct(TransactionService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        dd('RevertInvestorTransactions: Manually stopped');
        $this->log("----- START");
        $start = microtime(true);

        $investorId = (int) $this->argument('investorId');
        if (empty($investorId)) {
            dd('Error: No investorId provided');
        }
        dump("Investor #" . $investorId);

        try {

            $query1 = "
                select
                    w.wallet_id,
                    w.currency_id,
                    ba.bank_account_id
                from wallet w
                join bank_account ba ON (
                    ba.investor_id = w.investor_id
                    and ba.default = 1
                )
                where
                    w.investor_id = " . $investorId . "
                    and w.active = 1
                    and w.deleted = 0
            ";
            $paymentData = current(DB::select(DB::raw($query1)));
            if (empty($paymentData->bank_account_id)) {
                dd('Error: Wrong payment credentials(bank_account_id)');
            }
            if (empty($paymentData->wallet_id)) {
                dd('Error: Wrong payment credentials(wallet_id)');
            }
            dump('$paymentData', $paymentData);


            $wallet = Wallet::where('wallet_id', $paymentData->wallet_id)->first();
            if (empty($wallet->wallet_id)) {
                dd('Error: Can not get wallet');
            }


            $query2 = "
                select
                    ii.loan_id,
                    ii.investment_id,
                    sum(ii.principal) as principal
                from investor_installment ii
                where
                    ii.investor_id = " . $investorId . "
                    and paid = 0
                group by
                    ii.loan_id,
                    ii.investment_id;
            ";
            $results = DB::select(DB::raw($query2));
            dump('count = ' . count($results));


            $totalPrincipal = 0;
            $transactions = [];

            foreach ($results as $key => $row) {
                $transaction = $this->service->repaymentLoan(
                    $row->principal,
                    0,
                    0,
                    0,
                    $row->loan_id,
                    $investorId,
                    $paymentData->wallet_id,
                    $paymentData->currency_id,
                    $paymentData->bank_account_id,
                    $row->investment_id
                );

                if (!empty($transaction->transaction_id)) {
                    // investor plan close
                    DB::select(
                        DB::raw("
                            UPDATE investor_installment
                            SET paid = 1, paid_at = '" . (Carbon::now())->format('Y-m-d H:i:s') . "'
                            WHERE
                                investor_id = :investorId
                                and investment_id = :investment_id
                                and paid = 0
                        "),
                        [
                            'investorId' => $investorId,
                            'investment_id' => $row->investment_id,
                        ],
                    );

                    // wallet update
                    $wallet->addIncomeAmounts($row->principal, 0);

                    $transactions[] = $row;
                    $totalPrincipal += $row->principal;
                }
            }


        } catch (Throwable $e) {
            dump($e);
        }


        dump('Handled: ' . count($transactions) . ' loan(s)');
        dump('Restored: ' . $totalPrincipal . ' EUR');
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
    }


    public function handleOne()
    {
        $this->log("----- START");
        $start = microtime(true);


        try {
            $investorId = (int) $this->argument('investorId');
            $handlePaid = ($this->argument('handlePaid') == 1 ? true : false);
            $this->log('investorId: ' . $investorId . ', handlePaid: ' . $handlePaid);


            $results = DB::select(DB::raw("
                select
                    res.*,
                    i2.seq_num,
                    i2.due_date as installment_due_date
                from (
                    select
                        i.investor_id ,
                        i.investment_id ,
                        i.loan_id ,
                        l.interest_rate_percent ,
                        i.created_at as buy_date,
                        i.amount as investment_amount,
                        i.percent as investment_percent,
                        iidata.investor_installment_id,
                        coalesce(iidata.installment_id , iihdata.installment_id) as installment_id,
                        coalesce(iidata.paid , iihdata.paid) as paid,
                        coalesce(iidata.principal , iihdata.principal) as principal,
                        coalesce(iidata.interest , iihdata.interest) as interest
                    from public.investment i
                    join loan l on l.loan_id = i.loan_id
                    left JOIN LATERAL (
                        SELECT ii.* FROM investor_installment ii
                        WHERE ii.loan_id = l.loan_id and i.investment_id = ii.investment_id
                        ORDER BY ii.installment_id ASC LIMIT 1
                    ) iidata ON true
                    left JOIN LATERAL (
                        SELECT iih.* FROM investor_installment_history iih
                        WHERE iih.loan_id = l.loan_id and i.investment_id = iih.investment_id
                        ORDER BY iih.installment_id ASC LIMIT 1
                    ) iihdata ON true
                    where i.investor_id = " . intval($investorId) . "
                ) as res
                join installment i2 on i2.installment_id = res.installment_id
            "));
            $this->log('Total : ' . count($results));



            $final = [];
            $finalUnpaid = [];
            foreach ($results as $key => $row) {

                $outstandingAmount = $row->investment_amount;
                $loanInterestRate = $row->interest_rate_percent;
                $buyDate = Carbon::parse($row->buy_date);
                $dueDate = Carbon::parse($row->installment_due_date);

                $prevDueDate = null;
                $prevInstallmentPaid = false;
                if ($row->seq_num > 1) {

                    $intallments = DB::select(DB::raw("
                        select i.*
                        from installment i
                        where
                            i.loan_id = " . intval($row->loan_id) . "
                            and i.seq_num < " . intval($row->seq_num) . "
                        order by i.seq_num desc
                        limit 1;
                    "));
                    $prevInstallment = current($intallments);

                    $prevDueDate = Carbon::parse($prevInstallment->due_date);

                    if ($prevInstallment->paid == 1) {
                        if (empty($prevInstallment->paid_at)) { // paid on loan upload
                            $prevInstallmentPaid = true;
                        } else {
                            $buyDate2 = clone $buyDate;
                            $buyDate2->hour(00);
                            $buyDate2->minute(00);
                            $buyDate2->second(00);

                            $prevInstPaidAt = Carbon::parse($prevInstallment->paid_at);
                            $buyDate2->hour(00);
                            $buyDate2->minute(00);
                            $buyDate2->second(00);

                            if ($prevInstPaidAt->gt($buyDate2)) {
                                $prevInstallmentPaid = true;
                            }
                        }
                    }
                }

                list($interest, $ownDays) = instCalc::calcInvestorInstallmentInterestData(
                    $outstandingAmount,
                    $loanInterestRate,
                    $buyDate,
                    $dueDate,
                    $prevDueDate,
                    $prevInstallmentPaid
                );


                if (bccomp($row->interest, $interest, 3)) {
                    $final[] = [
                        'investor_id' => $row->investor_id,
                        'loan_id' => $row->loan_id,
                        'installment_id' => $row->installment_id,
                        'investment_id' => $row->investment_id,
                        'invested_amount' => $row->investment_amount,
                        'investment_percent' => $row->investment_percent,
                        'paid' => $row->paid,
                        'old_interest' => $row->interest,
                        'new_interest' => $interest,
                    ];

                    if ($row->paid == 0) {
                        $finalUnpaid[] = [
                            'investor_id' => $row->investor_id,
                            'loan_id' => $row->loan_id,
                            'installment_id' => $row->installment_id,
                            'investor_installment_id' => $row->investor_installment_id,
                            'investment_id' => $row->investment_id,
                            'invested_amount' => $row->investment_amount,
                            'investment_percent' => $row->investment_percent,
                            'paid' => $row->paid,
                            'old_interest' => $row->interest,
                            'new_interest' => $interest,
                        ];
                    }
                }
            }



            dd($final, json_encode($finalUnpaid));


        } catch (Throwable $e) {
            dump($e);
        }


        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
    }
}
