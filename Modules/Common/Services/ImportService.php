<?php

namespace Modules\Common\Services;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\CronLog;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\RepaidInstallment;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Common\Libraries\Calculator\InstallmentCalculator as Calc;
use Modules\Common\Traits\ImportTrait;
use Modules\Common\Traits\PinTrait;
use Modules\Common\Traits\SettingsTrait;
use Modules\Core\Traits\BaseModelTrait;
use Throwable;

class ImportService
{
    use ImportTrait;
    use PinTrait;
    use SettingsTrait;

    const NEFIN_ONLINE_OFFICE_ID = 4;

    private string $db = 'sqlsrv_site';

    /////////////////////// GET DATA FROM NEFIN ///////////////////////

    /**
     * @param array $lenderIds
     * @param int $limit
     * @param int $offset
     *
     * @return collection
     */
    public function getLoans(
        array $lenderIds,
        int $limit = null,
        int $offset = 0
    ): array {
        $maxOverdueDays = \SettingFacade::getSettingValue(
            Setting::MAX_ACCEPTABLE_OVERDUE_DAYS_KEY
        );

        if (empty($maxOverdueDays)) {
            $maxOverdueDays = Setting::MAX_ACCEPTABLE_OVERDUE_DAYS_DEFAULT_VALUE;
        }

        $result = DB::connection($this->db)->select(
            DB::raw(
                "
                SELECT
                    '" . Originator::ID_ORIG_STIKCREDIT . "' AS originator_id,
                    credit.CREDIT_ID AS lender_id,
                    credit.CONTRACT_NUMBER AS contract_id,
                    IIF (credit.PERIOD_TYPE = 'MONTHLY', 'installments', 'payday') AS type,
                    IIF (credit.OFFICE_ID = '" . self::NEFIN_ONLINE_OFFICE_ID . "', '0', '1') AS from_office,
                    '" . Country::ID_BG . "' AS country_id,
                    '" . Currency::ID_BGN . "' AS currency_id,
                    FORMAT(credit.CONTRACT_DATE, 'yyyy-MM-dd') as lender_issue_date,
                    FORMAT(credit.CREDIT_ACQUITTAL_DATE, 'yyyy-MM-dd') as final_payment_date,
                    '0' AS prepaid_schedule_payments,
                    credit.CREDIT_PERRIOD AS period,
                    credit.OVERDUE_DAYS AS overdue_days,
                    CONVERT(DECIMAL(11,2), credit.CREDIT_AMOUNT) AS amount,
                    CONVERT(DECIMAL(11,2), credit.REST_CAPITAL) AS amount_afranga,
                    '" . Loan::STATUS_NEW . "' AS status,
                    customer.Bulstat as pin
                FROM BizCreditHeaders credit
                JOIN BizCustomers customer ON customer.CustomerID = credit.CUSTOMER_ID
                WHERE
                    credit.CREDIT_ID IN (" . implode(",", $lenderIds) . ")
                    AND credit.ACTIVE_Y_N = 'Y'
                    AND credit.status = N'Усвоен'
                    AND credit.REST_CAPITAL > 0
                    AND credit.OVERDUE_DAYS < " . intval($maxOverdueDays) . "
                ORDER BY credit.CREDIT_ID ASC
                " . (
                !empty($limit)
                    ? "OFFSET " . (int)$offset . " ROWS FETCH NEXT " . (int)$limit . " ROWS ONLY"
                    : ""
                )
            )
        );

        return $result;
    }

    public function getInstallmentsForCredits(
        array $creditIds,
        int $limit = 10000,
        int $offset = 0
    ): array {
        $result = DB::connection($this->db)->select(
            DB::raw(
                "
            SELECT
                cl.CREDIT_LINE_ID AS lender_installment_id,
                cl.CREDIT_ID AS lender_id,
                IIF('Y' = cl.PAYMENT_COMPLETE, 1, 0) as paid,
                FORMAT(cl.DUE_DATE, 'yyyy-MM-dd') as due_date,
                '" . Currency::ID_BGN . "' AS currency_id,
                CONVERT(DECIMAL(11,2), cl.CAPITAL) as principal
            FROM BizCreditLines cl
            WHERE
                cl.CREDIT_ID IN (" . implode(',', $creditIds) . ")
                AND cl.ROW_TYPE = 'CREDIT'
            ORDER BY cl.CREDIT_LINE_ID ASC
            OFFSET " . (int)$offset . " ROWS
            FETCH NEXT " . (int)$limit . " ROWS ONLY
        "
            )
        );

        $result = array_map(
            function ($value) {
                return (array)$value;
            },
            $result
        );

        return $result;
    }

    public function getContractNumbersByCreditIds(
        array $creditIds,
        int $limit = 1000,
        int $offset = 0
    ): array {
        $result = DB::connection(Loan::DB_SITE)->select(
            DB::raw("
                SELECT
                    c.CONTRACT_NUMBER AS contract_id,
                    c.CREDIT_ID AS lender_id
                FROM BizCreditHeaders c
                WHERE
                    c.CREDIT_ID IN (" . implode(',', $creditIds) . ")
                ORDER BY c.CREDIT_ID ASC
                OFFSET " . (int)$offset . " ROWS
                FETCH NEXT " . (int)$limit . " ROWS ONLY
            ")
        );

        $data = [];
        foreach ($result as $obj) {
            $data[$obj->lender_id] = $obj->contract_id;
        }

        return $data;
    }

    public function getRepaidInstallments(
        array $lenderInstallmentIds,
        int $limit = 0,
        int $offset = 0
    ): array {
        $rows = DB::connection($this->db)->select(
            DB::raw(
                "
            SELECT
                cl.CREDIT_ID,
                cl.CREDIT_LINE_ID
            FROM BizCreditLines cl
            WHERE
                cl.CREDIT_LINE_ID IN (" . implode(',', $lenderInstallmentIds) . ")
                AND cl.PAYMENT_COMPLETE = 'Y'
                AND cl.REST_CAPITAL = '0.0000'
            ORDER BY cl.CREDIT_LINE_ID ASC
            " . (
                !empty($limit)
                    ? "OFFSET " . (int)$offset . " ROWS FETCH NEXT " . (int)$limit . " ROWS ONLY"
                    : ""
                )
            )
        );

        $now = Carbon::now()->format('Y-m-d H:i:s');
        $result = [];
        array_walk(
            $rows,
            function ($row) use (&$result, $now) {
                $result[] = [
                    'lender_id' => $row->CREDIT_ID,
                    'lender_installment_id' => $row->CREDIT_LINE_ID,
                    'handled' => 0,
                    'created_at' => $now,
                    'created_by' => BaseModelTrait::getAdminId(),
                ];
            }
        );

        return $result;
    }

    /**
     * Important!
     * for late repaid loans we check for (repaid_date + 1 day) since we compare NOW(which is next day)
     *
     * @param array $lenderIds
     * @param int|integer $limit
     * @param int|integer $offset
     *
     * @return array
     */
    public function getRepaidLoans(
        array $lenderIds,
        int $limit = 0,
        int $offset = 0
    ): array {
        $rows = DB::connection($this->db)->select(
            DB::raw("
            SELECT
                c.CREDIT_ID,
                IIF (
                    GETDATE() < c.CREDIT_ACQUITTAL_DATE,
                    '" . RepaidLoan::TYPE_EARLY . "',
                    IIF (
                        GETDATE() > DATEADD(day, 1, c.CREDIT_ACQUITTAL_DATE),
                        '" . RepaidLoan::TYPE_LATE . "',
                        '" . RepaidLoan::TYPE_NORMAL . "'
                    )
                ) AS TYPE
            FROM BizCreditHeaders c
            WHERE
                c.CREDIT_ID IN (" . implode(',', $lenderIds) . ")
                AND c.STATUS = 'completed'
            ORDER BY c.CREDIT_ID ASC
            " . (
                !empty($limit)
                    ? "OFFSET " . (int)$offset . " ROWS FETCH NEXT " . (int)$limit . " ROWS ONLY"
                    : ""
                )
            )
        );

        $now = Carbon::now()->format('Y-m-d H:i:s');
        $result = [];
        array_walk(
            $rows,
            function ($row) use (&$result, $now) {
                $result[] = [
                    'lender_id' => $row->CREDIT_ID,
                    'repayment_type' => $row->TYPE,
                    'handled' => 0,
                    'created_at' => $now,
                    'created_by' => BaseModelTrait::getAdminId(),
                ];
            }
        );

        return $result;
    }

    /////////////////////// GET DATA FROM OUR DB ///////////////////////

    public function getExistingLoans(array $lenderIds): array
    {
        $collection = Loan::whereIn('lender_id', $lenderIds)
            ->get()
            ->pluck('lender_id', 'loan_id');

        if ($collection->count() > 0) {
            return $collection->all();
        }

        return [];
    }

    public function getLastImportedLoan(): ?Loan
    {
        return Loan::latest('loan_id')->first();
    }

    public function getNewLoansCount(): int
    {
        return Loan::where('status', '=', Loan::STATUS_NEW)->count();
    }

    public function getNewLoans(bool $fromOffice = false)
    {
        $where = [
            ['status', '=', Loan::STATUS_NEW]
        ];

        $fromDB = ['from_db', '=', Loan::DB_SITE];
        if ($fromOffice) {
            $fromDB = ['from_db', '=', Loan::DB_OFFICE];
        }

        $where[] = $fromDB;

        return Loan::where($where)->orderBy('loan_id', 'ASC');
    }

    public function getLoansWithoutContractId()
    {
        return Loan::where('status', '=', Loan::STATUS_ACTIVE)
            ->where('originator_id', '=', Originator::ID_ORIG_STIKCREDIT)
            ->where(function ($q) {
                $q->whereNull('contract_id')->orWhere('contract_id', '');
            })->orderBy('loan_id', 'ASC');
    }

    public function getUnpaidInstallmentsCount(): int
    {
        return DB::table('installment')
            ->join(
                'loan',
                function ($join) {
                    $join->on('loan.loan_id', '=', 'installment.loan_id')
                        ->where('loan.status', '=', Loan::STATUS_ACTIVE)
                        ->where('loan.unlisted', '=', 0);
                }
            )
            ->leftJoin(
                'repaid_installment',
                function ($join) {
                    $join->on('repaid_installment.lender_installment_id', '=', 'installment.lender_installment_id');
                }
            )
            ->whereNull('repaid_installment.handled')
            ->where('installment.paid', '=', 0)
            ->where('installment.due_date', '<', Carbon::now())
            ->select('installment.installment_id')
            ->count();
    }

    public function getUnpaidInstallments(int $loanId = null): Builder
    {
        $builder = DB::table('installment')
            ->join(
                'loan',
                function ($join) {
                    $join->on('loan.loan_id', '=', 'installment.loan_id')
                        ->where('loan.status', '=', Loan::STATUS_ACTIVE)
                        ->where('loan.unlisted', '=', 0);
                }
            )
            ->leftJoin(
                'repaid_installment',
                function ($join) {
                    $join->on('repaid_installment.lender_installment_id', '=', 'installment.lender_installment_id');
                }
            )
            ->whereNull('repaid_installment.handled')
            ->where('installment.paid', '=', 0)
            ->where('installment.due_date', '<', Carbon::now())
            ->select(DB::raw('installment.*, loan.from_db'));

        if (!empty($loanId)) {
            $builder->where('installment.loan_id', '=', $loanId);
        }

        $builder->orderBy('installment.installment_id', 'ASC');

        return $builder;
    }

    public function getNewRepaidInstallmentsCount(): int
    {
        return DB::table('repaid_installment')
            ->select('lender_id, lender_installment_id')
            ->where('handled', '=', 0)
            ->count();
    }

    public function getNewRepaidInstallments(
        int $loanId = null
    ) {
        $builder = RepaidInstallment::where('handled', '=', 0)
            ->orderBy('repaid_installment_id', 'asc');


        if (!empty($loanId)) {
            $loan = Loan::where('loan_id', $loanId)->first();
            if (!empty($loan->lender_id)) {
                $builder->where('lender_id', '=', $loan->lender_id);
            }
        }

        return $builder;
    }

    public function getNewRepaidLoansCount(): int
    {
        return DB::table('repaid_loan')
            ->select('lender_id')
            ->where('handled', '=', 0)
            ->count();
    }

    public function getNewRepaidLoans(int $loanId = null)
    {
        $builder = RepaidLoan::where('handled', '=', 0);

        if ($loanId) {
            $loan = Loan::where('loan_id', $loanId)->first();
            if (!empty($loan->lender_id)) {
                $builder->where('lender_id', '=', $loan->lender_id);
            }
        }

        return $builder->orderBy('repaid_loan_id', 'asc');
    }

    public function getActiveLoansCount(
        bool $unHandled = false,
        bool $hasAvailableAmount = null
    ): int
    {
        if ($unHandled) {
            return DB::table('loan')
                ->leftJoin('repaid_loan', function ($join) {
                    $join->on('repaid_loan.lender_id', '=', 'loan.lender_id')
                        ->where('repaid_loan.handled', '=', 0);
                })
                ->whereNull('repaid_loan.repaid_loan_id')
                ->where('loan.unlisted', '=', 0)
                ->where('loan.status', '=', Loan::STATUS_ACTIVE)
                ->select('loan.loan_id')
                ->count();
        }

        $builder = DB::table('loan')
            ->where('unlisted', '=', 0)
            ->where('status', '=', Loan::STATUS_ACTIVE);
        if (true === $hasAvailableAmount) {
            $builder->where('amount_available', '>', 0);
        }

        return $builder->select('loan_id')->count();
    }

    public function getActiveLoansDBSource(bool $unHandled = false)
    {
        if ($unHandled) {
            return DB::table('loan')
                ->select('loan.*')
                ->leftJoin('repaid_loan', function ($join) {
                    $join->on('repaid_loan.lender_id', '=', 'loan.lender_id')
                        ->where('repaid_loan.handled', '=', 0);
                })
                ->whereNull('repaid_loan.repaid_loan_id')
                ->where('unlisted', '=', 0)
                ->where('status', '=', Loan::STATUS_ACTIVE)
                ->orderBy('loan_id', 'ASC');
        }

        return DB::table('loan')
            ->where('unlisted', '=', 0)
            ->where('status', '=', Loan::STATUS_ACTIVE)
            ->orderBy('loan_id', 'ASC');
    }

    public function getActiveLoans(int $limit = null): array
    {
        $rows = DB::table('loan')
            ->where('unlisted', '=', 0)
            ->where('status', '=', Loan::STATUS_ACTIVE)
            ->select('loan_id')
            ->limit($limit)
            ->get();

        if (count($rows) < 1) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            $result[$row->lender_id] = $row->loan_id;
        }

        return $result;
    }

    /////////////////////// IMPORT DATA TO OUR DB ///////////////////////

    public function loansMassInsert(array $data): bool
    {
        return Loan::insert($data);
    }

    public function unlistedLoansMassInsert(array $data): bool
    {
        return UnlistedLoan::insert($data);
    }

    public function installmentsMassInsert(array $data): int
    {
        Installment::insert($data);
        return count($data);
    }

    public function addInstallmentsAndUpdateLoans(
        array $installmentsFromNefin,
        array $ourLoans
    ): array {
        $installmentsCount = 0;
        $loansCount = 0;

        // add additional fields to installments
        $importData = $this->prepareInstallmentsAndLoansPrepaidSchedule(
            $installmentsFromNefin,
            $ourLoans
        );

        if (!empty($importData['installments'])) {
            DB::beginTransaction();

            try {
                // multipple insert - installments
                $installmentsCount = $this->installmentsMassInsert(
                    $importData['installments']
                );

                // multipple update - loans status and prepaid schedules
                if ($installmentsCount > 0) {
                    $loansCount = $this->activateLoansAndAddPrepaidSchedule(
                        $importData['loans']
                    );
                }

                DB::commit();
            } catch (Throwable $e) {
                DB::rollback();
            }
        }

        return [
            'installments' => $installmentsCount,
            'loans' => $loansCount,
        ];
    }

    public function addRepaidInstallments(array $data)
    {
        return RepaidInstallment::insert($data);
    }

    public function addRepaidLoans(array $data)
    {
        return RepaidLoan::insert($data);
    }

    /**
     * [activateLoansAndAddPrepaidSchedule description]
     *
     * @param array $loans - format: [prepaid]
     *
     * @return bool
     */
    public function activateLoansAndAddPrepaidSchedule(array $loans): int
    {
        $loansCount = 0;
        foreach ($loans as $prepaidInstallmentsCount => $lenderIds) {
            Loan::whereIn('loan_id', $lenderIds)->update(
                [
                    'status' => Loan::STATUS_ACTIVE,
                    'prepaid_schedule_payments' => $prepaidInstallmentsCount,
                ]
            );
            $loansCount += count($lenderIds);
        }

        return $loansCount;
    }

    public function prepareLoans(
        array $loansToImport,
        array $creditIdsAndPercents,
        string $dbName
    ): array
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $originatorPercent = $this->getOriginatorPercent();

        $results = array_map(
            function ($value) use ($creditIdsAndPercents, $originatorPercent, $now, $dbName) {

                $pinData = $this->getAgeAndSex($value->pin);
                if (empty($pinData)) {
                    return false;
                }

                $value->borrower_age = $pinData['age'];
                $value->borrower_gender = (
                1 == $pinData['sex']
                    ? 'female'
                    : 'male'
                );
                unset($value->pin);

                $value->created_at = $now;
                $value->created_by = BaseModelTrait::getAdminId();

                if (empty($value->overdue_days) || $value->overdue_days < 0) {
                    $value->overdue_days = 0;
                }

                $value->payment_status = $this->getPaymentStatusByOverdue(
                    $value->overdue_days
                );
                unset($value->overdue_days);
                $value->buyback = '1';

                $value->from_db = $dbName;
                $value->interest_rate_percent = $creditIdsAndPercents[$value->lender_id];
                $value->contract_tempate_id = $this->getDocumentTemplateId(
                    $value->type,
                    $value->from_office
                );
                $value->assigned_origination_fee_share = $originatorPercent;


                $value->original_currency_id = $value->currency_id;
                $value->original_amount = $value->amount;
                $value->original_amount_afranga = $value->amount_afranga;
                $value->original_remaining_principal = $value->amount_afranga;
                $value->original_amount_available = Calc::getAvailableAmount(
                    $value->original_amount_afranga,
                    $originatorPercent
                );

                $value->currency_id = Currency::ID_EUR;
                $value->amount = Calc::toEuro($value->amount);
                $value->amount_afranga = Calc::toEuro($value->amount_afranga);
                $value->remaining_principal = $value->amount_afranga;
                $value->amount_available = Calc::toEuro($value->original_amount_available);

                return (array) $value;
            },
            $loansToImport
        );

        // returns only good records, remove from array FALSE elements
        return array_filter($results);
    }

    public function prepareUnlistedLoans(array $lenderIds): array
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        return array_map(
            function ($value) use ($now) {
                $value = (object)$value;
                $value->lender_id = $value->scalar;
                unset($value->scalar);

                $value->created_at = $now;
                $value->created_by = BaseModelTrait::getAdminId();
                $value->handled = 0;

                return (array)$value;
            },
            $lenderIds
        );
    }

    public function prepareInstallmentsAndLoansPrepaidSchedule(
        array $installmentsToImport,
        array $ourLoans
    ): array {
        // restructing the data we have, so we will prepare an array in format:
        // [loan_id] = [
        //      'loan' => $loan - loan entity
        //      'installments' => [] - an array with subarrays - installment data from Nefin
        // ]
        $loansWithInstallment = $this->groupLoansAndInstallments(
            $installmentsToImport,
            $ourLoans
        );

        $import = [];
        $loansPrepaidSchedule = [];
        $originatorPercent = $this->getOriginatorPercent();

        $now = Carbon::now();
        foreach ($loansWithInstallment as $loanId => $loanData) {
            // set main objects
            $loan = $loanData['loan'];
            $installments = $loanData['installments'];


            // vars we need for calculations
            $remaingPrincipalOriginal = $loan->original_amount; // we should start from begin, since there are could be paid installments and we need to calc remain for them too
            $remaingPrincipal = Calc::toEuro($remaingPrincipalOriginal);

            $interestPercent = $loan->interest_rate_percent;
            $listingDate = Carbon::parse($loan->created_at);


            // calc: installments:summs, seq_num, status; loan.prepaid_schedule_payments
            $paidInstallments = 0;
            $previousDueDate = null;
            $firstPrepaidInFuture = null;
            $previousInstallmentPaid = false;

            foreach ($installments as $key => $installment) {
                $dueDate = Carbon::parse($installment['due_date']);

                $installment['original_currency_id'] = $installment['currency_id'];
                $installment['original_remaining_principal'] = $remaingPrincipalOriginal;
                $installment['original_principal'] = $installment['principal'];


                $installment['currency_id'] = Currency::ID_EUR;
                $installment['remaining_principal'] = $remaingPrincipal;
                $installment['principal'] = Calc::toEuro($installment['principal']);


                $installment['seq_num'] = $key + 1;

                // IMPORTANT: we do calculations with original amounts, and then change their values to EUR
                $installment = $installment + Calc::calcInstallmentAmounts(
                    $installment['remaining_principal'],
                    $installment['principal'],
                    $interestPercent, // % common
                    $listingDate,
                    $dueDate,
                    $previousDueDate,
                    $previousInstallmentPaid,
                );

                $installment['original_interest'] = 0;
                if ($installment['interest'] > 0) {
                    $installment['original_interest'] = Calc::toBgn($installment['interest']);
                }

                // could be equal to 0.00
                // $installment['original_interest'] = $installment['interest'];
                // if ($installment['interest'] > 0) {
                //     $installment['interest'] = Calc::toEuro($installment['interest']);
                // }
                // $installment['total'] = Calc::toEuro($installment['total']);


                // calc paid installments
                if (1 == $installment['paid']) {
                    $paymentStatus = Installment::STATUS_PAID;
                    $paidInstallments++;
                    $ovedue = 0;
                    $previousInstallmentPaid = true;
                } else {
                    // if not paid, we can check for overdue
                    $ovedue = Calc::getOverdueDays($listingDate, $dueDate);
                    $paymentStatus = Installment::STATUS_SCHEDULED;
                    $previousInstallmentPaid = false;

                    // if it's a loan with prepaid installments in future,
                    // we should take the remaining principal not from loans rest capital,
                    // but from remaining principal of first unpaid installment
                    if (
                        null === $firstPrepaidInFuture
                        && $remaingPrincipal > $loan->remaining_principal
                        && $dueDate->gt($now)
                    ) {
                        $loan->details = $loan->details . '; Prepaid installment in future, set remaining_principal & '
                            . 'amounts from installment remaining_principal'
                            . '(' . $loan->remaining_principal . ' -> ' . $remaingPrincipal . ')';
                        $loan->amount_afranga = $remaingPrincipal;
                        $loan->remaining_principal = $remaingPrincipal;
                        $loan->amount_available = Calc::getAvailableAmount(
                            $remaingPrincipal,
                            $originatorPercent
                        );
                        $loan->save();

                        $firstPrepaidInFuture = true;
                    }
                }
                $installment['status'] = $this->getPaymentStatusByOverdue($ovedue);
                $installment['payment_status'] = $paymentStatus;


                // update previous date for next installment, for future comparements
                // only for installments with due date after listing date (they have interest)
                if (!empty($installment['interest'])) {
                    $previousDueDate = $dueDate;
                }


                // re-calc total remain principal for next installment
                $remaingPrincipal = Calc::round($remaingPrincipal - $installment['principal']);
                $remaingPrincipalOriginal = Calc::round($remaingPrincipalOriginal - $installment['original_principal']);


                $import[] = $installment;
            }


            $loansPrepaidSchedule[$paidInstallments][] = $loanId;
        }

        return [
            'installments' => $import,
            'loans' => $loansPrepaidSchedule,
        ];
    }

    public function groupLoansAndInstallments(
        array $installmentsToImport,
        array $ourLoans
    ): array {
        $loansWithInstallment = [];
        $now = Carbon::now()->format('Y-m-d H:i:s');

        array_walk(
            $installmentsToImport,
            function ($installment) use (&$loansWithInstallment, $ourLoans, $now) {
                $loan = $ourLoans[$installment['lender_id']];
                unset($installment['lender_id']);

                if (!isset($loansWithInstallment[$loan->loan_id])) {
                    $loansWithInstallment[$loan->loan_id] = [
                        'loan' => $loan,
                        'installments' => [],
                    ];
                }

                $installment['created_at'] = $now;
                $installment['created_by'] = BaseModelTrait::getAdminId();

                $installment['loan_id'] = $loan->loan_id;
                $loansWithInstallment[$loan->loan_id]['installments'][] = $installment;
            }
        );

        return $loansWithInstallment;
    }


    public function getExistingUnListedLoans(array $lenderIds)
    {
        $loanIds = Loan::whereIn('lender_id', $lenderIds)
            ->where('unlisted', 1)
            ->get()
            ->pluck('lender_id')
            ->toArray();

        $unlistedUnhandledLoanIds = UnlistedLoan::whereIn('lender_id', $lenderIds)
            ->where('handled', 0)
            ->get()
            ->pluck('lender_id')
            ->toArray();

        $allSkip = array_unique(array_merge($loanIds, $unlistedUnhandledLoanIds));

        if (count($allSkip) > 0) {
            return $allSkip;
        }

        return [];
    }

    public function setDb(string $db): void
    {
        $this->db = $db;
    }
}
