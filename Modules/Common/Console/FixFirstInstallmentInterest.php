<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Libraries\Calculator\InstallmentCalculator as instCalc;
use Throwable;

class FixFirstInstallmentInterest extends CommonCommand
{
    protected $name = 'fix:first-installment-interest';
    protected $signature = 'fix:first-installment-interest {investorId?} {handlePaid?}';
    protected $description = 'Fix';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        dd('FixFirstInstallmentInterest: Manually stopped');
        $this->log("----- START");
        $start = microtime(true);


        try {

//             $outstandingAmount = 92.03;
//             $loanInterestRate = 18;
//             $buyDate = Carbon::parse('2021-02-22 16:19:38');
//             $dueDate = Carbon::parse('2021-03-08');
//             $prevDueDate = null;
//             $prevInstallmentPaid = false;

//             list($interest, $ownDays) = instCalc::calcInvestorInstallmentInterestData(
//                 $outstandingAmount,
//                 $loanInterestRate,
//                 $buyDate,
//                 $dueDate,
//                 $prevDueDate,
//                 $prevInstallmentPaid
//             );

// dd('!!!', $interest, $ownDays);

            $query = "
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
                        iidata.installment_id,
                        iidata.paid,
                        iidata.principal,
                        iidata.interest
                    from investment i
                    join loan l on (
                        l.loan_id = i.loan_id
                        and l.unlisted = 0
                    )
                    JOIN LATERAL (
                        select ii.* FROM investor_installment ii
                        where
                            i.investment_id = ii.investment_id
                        order by ii.installment_id asc
                        limit 1
                    ) iidata ON true
                    where iidata.paid = 0
                ) as res
                join installment i2 on i2.installment_id = res.installment_id
            ";

            $final = [];
            $total = 0;

            DB::query()->fromSub($query, 'alias')->orderBy('alias.investor_installment_id')->chunk(
                2000,
                function ($results) use (&$final, &$total) {

dump('total = ' . $total . ', count = ' . count($final));
$total += count($results);

                    foreach ($results as $key => $row) {

                        $interest = 0;
                        $outstandingAmount = null;
                        $loanInterestRate = null;
                        $buyDate = null;
                        $dueDate = null;
                        $prevDueDate = null;
                        $prevInstallmentPaid = null;

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


                        if (
                            bccomp($row->interest, $interest, 3)
                            && (
                                (floatval($interest) - floatval($row->interest)) >= 2
                                || (floatval($row->interest) - floatval($interest)) >= 2
                            )
                        ) {
                            $final[] = [
                                'investor_id' => $row->investor_id,
                                'loan_id' => $row->loan_id,
                                'installment_id' => $row->installment_id,
                                'investment_id' => $row->investment_id,
                                'invested_amount' => $row->investment_amount,
                                'investment_percent' => $row->investment_percent,
                                'buy_date' => $buyDate->format('Y-m-d'),
                                'due_date' => $dueDate->format('Y-m-d'),
                                'prev_due_date' => is_null($prevDueDate) ? 'null' : $prevDueDate->format('Y-m-d'),
                                'prev_inst_paid' => $prevInstallmentPaid,
                                'paid' => $row->paid,
                                'old_interest' => $row->interest,
                                'new_interest' => $interest,
                            ];
                        }
                    }
                }
            );

//             $final = [];
//             // DB::table(DB::raw($query))->chunkById(
//             DB::select($query)->chunkById(
//                 200,
//                 function($results) {

//                     foreach ($results as $key => $row) {
// dump('count = ' . count($final));

//                         $outstandingAmount = $row->investment_amount;
//                         $loanInterestRate = $row->interest_rate_percent;
//                         $buyDate = Carbon::parse($row->buy_date);
//                         $dueDate = Carbon::parse($row->installment_due_date);

//                         $prevDueDate = null;
//                         $prevInstallmentPaid = false;
//                         if ($row->seq_num > 1) {

//                             $intallments = DB::select(DB::raw("
//                                 select i.*
//                                 from installment i
//                                 where
//                                     i.loan_id = " . intval($row->loan_id) . "
//                                     and i.seq_num < " . intval($row->seq_num) . "
//                                 order by i.seq_num desc
//                                 limit 1;
//                             "));
//                             $prevInstallment = current($intallments);

//                             $prevDueDate = Carbon::parse($prevInstallment->due_date);

//                             if ($prevInstallment->paid == 1) {
//                                 if (empty($prevInstallment->paid_at)) { // paid on loan upload
//                                     $prevInstallmentPaid = true;
//                                 } else {
//                                     $buyDate2 = clone $buyDate;
//                                     $buyDate2->hour(00);
//                                     $buyDate2->minute(00);
//                                     $buyDate2->second(00);

//                                     $prevInstPaidAt = Carbon::parse($prevInstallment->paid_at);
//                                     $buyDate2->hour(00);
//                                     $buyDate2->minute(00);
//                                     $buyDate2->second(00);

//                                     if ($prevInstPaidAt->gt($buyDate2)) {
//                                         $prevInstallmentPaid = true;
//                                     }
//                                 }
//                             }
//                         }

//                         list($interest, $ownDays) = instCalc::calcInvestorInstallmentInterestData(
//                             $outstandingAmount,
//                             $loanInterestRate,
//                             $buyDate,
//                             $dueDate,
//                             $prevDueDate,
//                             $prevInstallmentPaid
//                         );


//                         if (bccomp($row->interest, $interest, 3)) {
//                             $final[] = [
//                                 'investor_id' => $row->investor_id,
//                                 'loan_id' => $row->loan_id,
//                                 'installment_id' => $row->installment_id,
//                                 'investment_id' => $row->investment_id,
//                                 'invested_amount' => $row->investment_amount,
//                                 'investment_percent' => $row->investment_percent,
//                                 'paid' => $row->paid,
//                                 'old_interest' => $row->interest,
//                                 'new_interest' => $interest,
//                             ];
//                         }
//                     }
//                 },
//                 'investor_installment_id'
//             );


            dump('final', json_encode($final), 'final = ' . count($final));



            // InvestorInstallment



        } catch (Throwable $e) {
            dump($e);
        }


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
