<?php

namespace Tests\Unit\Invest;

use Carbon\Carbon;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\InvestService;
use Tests\TestCase;

class InvestorInstallmentInterestPrincipalDaysTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->service = \App::make(InvestService::class);
    }

    /**
     * 1st string:
     * <---|▓|--0-----0-----0----
     */
    public function testInvestorPlanWhenBuyDateBeforeFirstInstallmentDate()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2020-12-15 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 118.40;
        $loan->remaining_principal = 118.40;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 68.00;
        $investment->percent = 57.432432;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );


        // TEST VALUES
        $this->assertEquals(count($res), 3);
        // 1st inst
        $this->assertEquals($res[0]['remaining_principal'], 68.00);
        $this->assertEquals($res[0]['principal'], 22.51);
        $this->assertEquals($res[0]['interest'], 0.26);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 22.77);
        $this->assertEquals($res[0]['days'], 10);
        // 2nd inst
        $this->assertEquals($res[1]['remaining_principal'], 45.49);
        $this->assertEquals($res[1]['principal'], 22.65);
        $this->assertEquals($res[1]['interest'], 0.55);
        $this->assertEquals($res[1]['accrued_interest'], 0);
        $this->assertEquals($res[1]['late_interest'], 0);
        $this->assertEquals($res[1]['total'], 23.20);
        $this->assertEquals($res[1]['days'], 31);
        // 3rd inst
        $this->assertEquals($res[2]['remaining_principal'], 22.84);
        $this->assertEquals($res[2]['principal'], 22.84);
        $this->assertEquals($res[2]['interest'], 0.28);
        $this->assertEquals($res[2]['accrued_interest'], 0);
        $this->assertEquals($res[2]['late_interest'], 0);
        $this->assertEquals($res[2]['total'], 23.12);
        $this->assertEquals($res[2]['days'], 31);
    }

    /**
     * 2nd string:
     * <---|0|-----0-----0----
     */
    public function testInvestorPlanWhenBuyDateIsSameAsFirstInstallmentDate()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2020-12-25 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 118.40;
        $loan->remaining_principal = 118.40;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 68.00;
        $investment->percent = 57.432432;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );


        // TEST VALUES
        $this->assertEquals(count($res), 3);
        // 1st inst
        $this->assertEquals($res[0]['remaining_principal'], 68.00);
        $this->assertEquals($res[0]['principal'], 22.51);
        $this->assertEquals($res[0]['interest'], 0.00);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 22.51);
        $this->assertEquals($res[0]['days'], 0);
        // 2nd inst
        $this->assertEquals($res[1]['remaining_principal'], 45.49);
        $this->assertEquals($res[1]['principal'], 22.65);
        $this->assertEquals($res[1]['interest'], 0.55);
        $this->assertEquals($res[1]['accrued_interest'], 0);
        $this->assertEquals($res[1]['late_interest'], 0);
        $this->assertEquals($res[1]['total'], 23.20);
        $this->assertEquals($res[1]['days'], 31);
        // 3rd inst
        $this->assertEquals($res[2]['remaining_principal'], 22.84);
        $this->assertEquals($res[2]['principal'], 22.84);
        $this->assertEquals($res[2]['interest'], 0.28);
        $this->assertEquals($res[2]['accrued_interest'], 0);
        $this->assertEquals($res[2]['late_interest'], 0);
        $this->assertEquals($res[2]['total'], 23.12);
        $this->assertEquals($res[2]['days'], 31);
    }

    /**
     * 3rd string:
     * <----0--|▓|--0----0-----
     */
    public function testInvestorPlanWhenBuyDateIBetweenNonPaidInstallments()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2021-01-14 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 118.40;
        $loan->remaining_principal = 118.40;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 68.00;
        $investment->percent = 57.432432;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );


        // TEST VALUES
        $this->assertEquals(count($res), 3);
        // 1st inst
        $this->assertEquals($res[0]['remaining_principal'], 68.00);
        $this->assertEquals($res[0]['principal'], 22.51);
        $this->assertEquals($res[0]['interest'], 0.00);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 22.51);
        $this->assertEquals($res[0]['days'], 0);
        // 2nd inst
        $this->assertEquals($res[1]['remaining_principal'], 45.49);
        $this->assertEquals($res[1]['principal'], 22.65);
        $this->assertEquals($res[1]['interest'], 0.19);
        $this->assertEquals($res[1]['accrued_interest'], 0);
        $this->assertEquals($res[1]['late_interest'], 0);
        $this->assertEquals($res[1]['total'], 22.84);
        $this->assertEquals($res[1]['days'], 11);
        // 3rd inst
        $this->assertEquals($res[2]['remaining_principal'], 22.84);
        $this->assertEquals($res[2]['principal'], 22.84);
        $this->assertEquals($res[2]['interest'], 0.28);
        $this->assertEquals($res[2]['accrued_interest'], 0);
        $this->assertEquals($res[2]['late_interest'], 0);
        $this->assertEquals($res[2]['total'], 23.12);
        $this->assertEquals($res[2]['days'], 31);
    }

    /**
     * 4th string:
     * <----Ø----|▓|---0---
     * @return void
     */
    public function testInvestorPlanWhenInvestForLoanWithPaidInstallmentBefore()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2021-01-14 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 79.20;
        $loan->remaining_principal = 79.20;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 1;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'paid';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 68.00;
        $investment->percent = 85.85858586;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );


        // TEST VALUES
        // 1st inst
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['remaining_principal'], 68.00);
        $this->assertEquals($res[0]['principal'], 33.86);
        $this->assertEquals($res[0]['interest'], 0.29);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 34.15);
        $this->assertEquals($res[0]['days'], 11);
        // 3rd inst
        $this->assertEquals($res[1]['remaining_principal'], 34.14);
        $this->assertEquals($res[1]['principal'], 34.14);
        $this->assertEquals($res[1]['interest'], 0.41);
        $this->assertEquals($res[1]['accrued_interest'], 0);
        $this->assertEquals($res[1]['late_interest'], 0);
        $this->assertEquals($res[1]['total'], 34.55);
        $this->assertEquals($res[1]['days'], 31);
    }

    /**
     * Loan listed on our platform.
     * There are several paid installments. The last one is paid but 5 days later then due date.
     * Investor invest the day after the installment paid
     *
     * Test for Calculations Checks on investor plan
     */
    public function testInvestorPlanWhenInvestForLoanWithPaidInstallmentBeforeReal()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2021-04-16 04:24:56');
        $listingDate = Carbon::parse('2021-03-25 10:58:39');


        $loan = new Loan();
        $loan->loan_id = 246728;
        $loan->originator_id = 1;
        $loan->lender_id = 102069;
        $loan->type = 'installments';
        $loan->from_office = 0;
        $loan->country_id = 38;
        $loan->lender_issue_date = '2020-12-10';
        $loan->final_payment_date = '2021-10-10';
        $loan->prepaid_schedule_payments = 4;
        $loan->period = 10;
        $loan->currency_id = 1;
        $loan->amount = 1022.58;
        $loan->amount_afranga = 830.36;
        $loan->amount_available = 4.40;
        $loan->remaining_principal = 749.52;
        $loan->interest_rate_percent = 16.80;
        $loan->assigned_origination_fee_share = 10.00;
        $loan->buyback = 1;
        $loan->contract_tempate_id = 1;
        $loan->borrower_age = 49;
        $loan->borrower_gender = 'male';
        $loan->status = 'active';
        $loan->payment_status = 'current';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;


        $investment = new Investment();
        $investment->investment_id = 103060;
        $investment->investment_bunch_id = 8513;
        $investment->investor_id = 103958;
        $investment->wallet_id = 10367;
        $investment->loan_id = 246728;
        $investment->amount = 20.00;
        $investment->percent = 2.6683744263;
        $investment->created_at = $buyDate;


        $installments = [];
        if (true) {
            $inst1 = new Installment();
            $inst1->installment_id = 98634;
            $inst1->loan_id = 246728;
            $inst1->lender_installment_id = 810201;
            $inst1->seq_num = 1;
            $inst1->due_date = '2021-01-10';
            $inst1->paid = 1;
            $inst1->paid_at = null;
            $inst1->remaining_principal = 1022.58;
            $inst1->principal = 56.65;
            $inst1->accrued_interest = 0.00;
            $inst1->interest = 0.00;
            $inst1->late_interest = 0.00;
            $inst1->total = 56.65;
            $inst1->status = 'current';
            $inst1->payment_status = 'paid';
            $inst1->created_at = $listingDate;
            $installments[] = $inst1;

            $inst2 = new Installment();
            $inst2->installment_id = 98635;
            $inst2->loan_id = 246728;
            $inst2->lender_installment_id = 810201;
            $inst2->seq_num = 2;
            $inst2->due_date = '2021-02-10';
            $inst2->paid = 1;
            $inst2->paid_at = null;
            $inst2->remaining_principal = 965.93;
            $inst2->principal = 63.77;
            $inst2->accrued_interest = 0.00;
            $inst2->interest = 0.00;
            $inst2->late_interest = 0.00;
            $inst2->total = 63.77;
            $inst2->status = 'current';
            $inst2->payment_status = 'paid';
            $inst2->created_at = $listingDate;
            $installments[] = $inst2;

            $inst3 = new Installment();
            $inst3->installment_id = 98636;
            $inst3->loan_id = 246728;
            $inst3->lender_installment_id = 810201;
            $inst3->seq_num = 3;
            $inst3->due_date = '2021-03-10';
            $inst3->paid = 1;
            $inst3->paid_at = null;
            $inst3->remaining_principal = 902.16;
            $inst3->principal = 71.80;
            $inst3->accrued_interest = 0.00;
            $inst3->interest = 0.00;
            $inst3->late_interest = 0.00;
            $inst3->total = 71.80;
            $inst3->status = 'current';
            $inst3->payment_status = 'paid';
            $inst3->created_at = $listingDate;
            $installments[] = $inst3;

            $inst4 = new Installment();
            $inst4->installment_id = 98637;
            $inst4->loan_id = 246728;
            $inst4->lender_installment_id = 810201;
            $inst4->seq_num = 4;
            $inst4->due_date = '2021-04-10';
            $inst4->paid = 1;
            $inst4->paid_at = Carbon::parse('2021-04-15 23:59:59');
            $inst4->remaining_principal = 830.36;
            $inst4->principal = 80.84;
            $inst4->accrued_interest = 0.00;
            $inst4->interest = 6.20;
            $inst4->late_interest = 87.04;
            $inst4->total = 71.80;
            $inst4->status = '1-15 days';
            $inst4->payment_status = 'paid late';
            $inst4->created_at = $listingDate;
            $installments[] = $inst4;

            $inst5 = new Installment();
            $inst5->installment_id = 98638;
            $inst5->loan_id = 246728;
            $inst5->lender_installment_id = 810201;
            $inst5->seq_num = 5;
            $inst5->due_date = '2021-05-10';
            $inst5->paid = 0;
            $inst5->paid_at = null;
            $inst5->remaining_principal = 749.52;
            $inst5->principal = 91.00;
            $inst5->accrued_interest = 0.00;
            $inst5->interest = 10.49;
            $inst5->late_interest = 87.04;
            $inst5->total = 101.49;
            $inst5->status = 'current';
            $inst5->payment_status = 'scheduled';
            $inst5->created_at = $listingDate;
            $installments[] = $inst5;

            $inst6 = new Installment();
            $inst6->installment_id = 98639;
            $inst6->loan_id = 246728;
            $inst6->lender_installment_id = 810201;
            $inst6->seq_num = 6;
            $inst6->due_date = '2021-06-10';
            $inst6->paid = 0;
            $inst6->paid_at = null;
            $inst6->remaining_principal = 658.52;
            $inst6->principal = 102.46;
            $inst6->accrued_interest = 0.00;
            $inst6->interest = 9.53;
            $inst6->late_interest = 87.04;
            $inst6->total = 111.99;
            $inst6->status = 'current';
            $inst6->payment_status = 'scheduled';
            $inst6->created_at = $listingDate;
            $installments[] = $inst6;

            $inst7 = new Installment();
            $inst7->installment_id = 98640;
            $inst7->loan_id = 246728;
            $inst7->lender_installment_id = 810201;
            $inst7->seq_num = 7;
            $inst7->due_date = '2021-07-10';
            $inst7->paid = 0;
            $inst7->paid_at = null;
            $inst7->remaining_principal = 556.06;
            $inst7->principal = 115.35;
            $inst7->accrued_interest = 0.00;
            $inst7->interest = 7.78;
            $inst7->late_interest = 0.00;
            $inst7->total = 123.13;
            $inst7->status = 'current';
            $inst7->payment_status = 'scheduled';
            $inst7->created_at = $listingDate;
            $installments[] = $inst7;

            $inst8 = new Installment();
            $inst8->installment_id = 98641;
            $inst8->loan_id = 246728;
            $inst8->lender_installment_id = 810201;
            $inst8->seq_num = 8;
            $inst8->due_date = '2021-08-10';
            $inst8->paid = 0;
            $inst8->paid_at = null;
            $inst8->remaining_principal = 440.71;
            $inst8->principal = 129.87;
            $inst8->accrued_interest = 0.00;
            $inst8->interest = 6.38;
            $inst8->late_interest = 0.00;
            $inst8->total = 136.25;
            $inst8->status = 'current';
            $inst8->payment_status = 'scheduled';
            $inst8->created_at = $listingDate;
            $installments[] = $inst8;

            $inst9 = new Installment();
            $inst9->installment_id = 98642;
            $inst9->loan_id = 246728;
            $inst9->lender_installment_id = 810201;
            $inst9->seq_num = 9;
            $inst9->due_date = '2021-09-10';
            $inst9->paid = 0;
            $inst9->paid_at = null;
            $inst9->remaining_principal = 310.84;
            $inst9->principal = 146.21;
            $inst9->accrued_interest = 0.00;
            $inst9->interest = 4.50;
            $inst9->late_interest = 0.00;
            $inst9->total = 150.71;
            $inst9->status = 'current';
            $inst9->payment_status = 'scheduled';
            $inst9->created_at = $listingDate;
            $installments[] = $inst9;

            $inst10 = new Installment();
            $inst10->installment_id = 98643;
            $inst10->loan_id = 246728;
            $inst10->lender_installment_id = 810201;
            $inst10->seq_num = 10;
            $inst10->due_date = '2021-10-10';
            $inst10->paid = 0;
            $inst10->paid_at = null;
            $inst10->remaining_principal = 164.63;
            $inst10->principal = 164.64;
            $inst10->accrued_interest = 0.00;
            $inst10->interest = 2.30;
            $inst10->late_interest = 0.00;
            $inst10->total = 166.94;
            $inst10->status = 'current';
            $inst10->payment_status = 'scheduled';
            $inst10->created_at = $listingDate;
            $installments[] = $inst10;
        }


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );

        // TEST VALUES
        // 1st inst
        $this->assertEquals($res[0]['principal'], 2.43);
        $this->assertEquals($res[0]['interest'], 0.22);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 2.65);
        $this->assertEquals($res[0]['days'], 24);
        // $this->assertEquals($res[0]['installment_days'], 30);
        // 2nd inst
        $this->assertEquals($res[1]['principal'], 2.73);
        $this->assertEquals($res[1]['interest'], 0.25);
        $this->assertEquals($res[1]['accrued_interest'], 0);
        $this->assertEquals($res[1]['late_interest'], 0);
        $this->assertEquals($res[1]['total'], 2.98);
        $this->assertEquals($res[1]['days'], 31);
        // $this->assertEquals($res[1]['installment_days'], 31);
        // 6th inst
        $this->assertEquals($res[5]['principal'], 4.39);
        $this->assertEquals($res[5]['interest'], 0.06);
        $this->assertEquals($res[5]['accrued_interest'], 0);
        $this->assertEquals($res[5]['late_interest'], 0);
        $this->assertEquals($res[5]['total'], 4.45);
        $this->assertEquals($res[5]['days'], 30);
        // $this->assertEquals($res[5]['installment_days'], 30);
    }

    /**
     * 5th string:
     * <----Ø------Ø---|▓|--Ø----Ø-----0---
     */
    public function testPaidInstallmentsBeforeListingLateBiggerInvestAmount()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2021-01-14 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 39.76;
        $loan->remaining_principal = 39.76;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 1;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'paid';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 1;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 68.00;
        $investment->percent = 171.026157;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );

        // TEST VALUES
        $this->assertEquals(count($res), 1);
        // 3rd inst
        $this->assertEquals($res[0]['remaining_principal'], 68.00);
        $this->assertEquals($res[0]['principal'], 68.00);
        $this->assertEquals($res[0]['interest'], 1.11);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 69.11);
        $this->assertEquals($res[0]['days'], 42);
    }

    public function testPaidInstallmentsBeforeListingLate()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2021-01-14 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 39.76;
        $loan->remaining_principal = 39.76;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 1;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'paid';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 1;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 25.00;
        $investment->percent = 62.877264;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );

        // TEST VALUES
        $this->assertEquals(count($res), 1);
        // 3rd inst
        $this->assertEquals($res[0]['remaining_principal'], 25.00);
        $this->assertEquals($res[0]['principal'], 25.00);
        $this->assertEquals($res[0]['interest'], 0.41);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 25.41);
        $this->assertEquals($res[0]['days'], 42);
    }

    /**
     * 6th string:
     * <----O--[O]--O----
     */
    public function testBuyDateIsOnDueDateAndBetween2UnpaidInstallments()
    {
        // PREPARE TEST DATA
        $buyDate = Carbon::parse('2021-01-25 04:24:56');
        $listingDate = Carbon::parse('2020-11-25 10:58:39');

        $loanId = rand(10000, 99999);
        $loan = new Loan();
        $loan->loan_id = $loanId;
        $loan->type = 'installments';
        $loan->lender_issue_date = $listingDate;
        $loan->prepaid_schedule_payments = 4;
        $loan->amount_available = 118.40;
        $loan->remaining_principal = 118.40;
        $loan->interest_rate_percent = 14;
        $loan->status = 'active';
        $loan->unlisted = 0;
        $loan->created_at = $listingDate;

        $installments = [];
        if (true) {
            $instBasis = rand(10000, 99999);

            $seq = 1;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2020-12-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 118.40;
            $$instName->principal = 39.20;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 1.38;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.58;
            $$instName->status = 'current';
            $$instName->payment_status = 'overdue';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;


            $seq = 2;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-01-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 79.20;
            $$instName->principal = 39.44;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.95;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.39;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;

            $seq = 3;
            $instName = 'inst' . $seq;
            $$instName = new Installment();
            $$instName->installment_id = $instBasis + $seq;
            $$instName->loan_id = $loanId;
            $$instName->seq_num = $seq;
            $$instName->due_date = '2021-02-25';
            $$instName->paid = 0;
            $$instName->paid_at = null;
            $$instName->remaining_principal = 39.76;
            $$instName->principal = 39.76;
            $$instName->accrued_interest = 0.00;
            $$instName->interest = 0.48;
            $$instName->late_interest = 0.00;
            $$instName->total = 40.24;
            $$instName->status = 'current';
            $$instName->payment_status = 'scheduled';
            $$instName->created_at = $listingDate;
            $installments[] = $$instName;
        }

        $investment = new Investment();
        $investment->investment_id = rand(10000, 99999);
        $investment->investment_bunch_id = rand(10000, 99999);
        $investment->investor_id = rand(10000, 99999);
        $investment->wallet_id = rand(10000, 99999);
        $investment->loan_id = $loanId;
        $investment->amount = 68.00;
        $investment->percent = 57.432432;
        $investment->created_at = $buyDate;


        // PREPARE INVESTOR PLAN
        $res = $this->service->prepareInvestorInstallments(
            $loan,
            $investment,
            $installments,
            $buyDate
        );


        // TEST VALUES
        $this->assertEquals(count($res), 3);
        // 1st inst
        $this->assertEquals($res[0]['remaining_principal'], 68.00);
        $this->assertEquals($res[0]['principal'], 22.51);
        $this->assertEquals($res[0]['interest'], 0.00);
        $this->assertEquals($res[0]['accrued_interest'], 0);
        $this->assertEquals($res[0]['late_interest'], 0);
        $this->assertEquals($res[0]['total'], 22.51);
        $this->assertEquals($res[0]['days'], 0);
        // 2nd inst
        $this->assertEquals($res[1]['remaining_principal'], 45.49);
        $this->assertEquals($res[1]['principal'], 22.65);
        $this->assertEquals($res[1]['interest'], 0.00);
        $this->assertEquals($res[1]['accrued_interest'], 0);
        $this->assertEquals($res[1]['late_interest'], 0);
        $this->assertEquals($res[1]['total'], 22.65);
        $this->assertEquals($res[1]['days'], 0);
        // 3rd inst
        $this->assertEquals($res[2]['remaining_principal'], 22.84);
        $this->assertEquals($res[2]['principal'], 22.84);
        $this->assertEquals($res[2]['interest'], 0.28);
        $this->assertEquals($res[2]['accrued_interest'], 0);
        $this->assertEquals($res[2]['late_interest'], 0);
        $this->assertEquals($res[2]['total'], 23.12);
        $this->assertEquals($res[2]['days'], 31);
    }
}
