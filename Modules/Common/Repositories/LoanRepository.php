<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Loan;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Modules\Core\Repositories\BaseRepository;

class LoanRepository extends BaseRepository
{
    public function getAll(string $selectQuery = ''): Builder
    {
        $builder = DB::table('loan');
        $builder->select(DB::raw('loan.*'));
        return $builder;
    }

    /**
     * @param int $loanId
     *
     * @return mixed
     */
    public function getById(int $loanId)
    {
        return Loan::where(
            'loan_id',
            '=',
            $loanId
        )->first();
    }

    /**
     * @param int $loanId
     * @param array $data
     *
     * @return mixed
     */
    public function loanUpdate(int $loanId, array $data)
    {
        return Loan::where('loan_id', '=', $loanId)->update($data);
    }

    /**
     * @param array $where
     *
     * @return mixed
     */
    public function getLoanByFilters(
        array $where = []
    ) {
        $builder = DB::table('loan');
        $builder->select(
            DB::raw(
                '
            loan.*
            '
            )
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        $builder->orderBy('loan_id', 'asc');

        return $builder->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLoansWithFirstUnpaidInstallment(int $loanId = null)
    {
        $where = '(
            DATE(l.payment_status_updated_at) < CURRENT_DATE
            OR l.payment_status_updated_at IS NULL
        )';
        if (!empty($loanId)) {
            $where = 'l.loan_id = ' . $loanId;
        }

        $query = DB::select(
            DB::raw(
                "
                SELECT
                    l.*,
                    i.installment_id,
                    i.due_date AS installment_due_date,
                    i.status AS installment_status
                FROM loan AS l
                JOIN installment AS i ON i.installment_id = (
                    SELECT installment_id
                    FROM installment
                    WHERE installment.loan_id = l.loan_id
                    AND installment.paid = 0
                    ORDER BY installment.due_date
                    LIMIT 1
                )
                WHERE
                    l.status = :active_status
                    AND l.unlisted = 0
                    AND " . $where
            ),
            ['active_status' => Loan::STATUS_ACTIVE]
        );

        $result = Loan::hydrate($query);

        return $result;
    }

    /**
     * @param Loan $loan
     * @param string $paymentStatus
     *
     * @return Loan
     */
    public function changePaymentStatus(Loan $loan, string $paymentStatus)
    {
        $loan->payment_status = $paymentStatus;
        $loan->payment_status_updated_at = Carbon::now();
        $loan->save();

        return $loan;
    }

    public function refreshLoanPaymentStatusUpdatedAt(Loan $loan)
    {
        $loan->payment_status_updated_at = Carbon::now();
        $loan->save();

        return $loan;
    }

    /**
     * @param Loan $loan
     *
     * @return Loan
     */
    public function refreshInterestUpdatedAt(Loan $loan)
    {
        $loan->interest_updated_at = Carbon::now();
        $loan->save();

        return $loan;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoansWithInvestments(int $loanId = null)
    {
        $autoCondition = "AND (
            DATE(l.interest_updated_at) < CURRENT_DATE
            OR l.interest_updated_at IS NULL
        )";

        $query = DB::select(
            DB::raw(
                "
                SELECT
                    l.*
                FROM loan AS l
                INNER JOIN investment AS i ON i.loan_id = l.loan_id
                WHERE
                    l.status = :active_status
                    AND l.unlisted = 0
                    " . (!empty($loanId) ? " AND l.loan_id = " . intval($loanId) : $autoCondition) . "
                GROUP BY l.loan_id
            "
            ),
            ['active_status' => Loan::STATUS_ACTIVE]
        );

        $result = Loan::hydrate($query);

        return $result;
    }

    /*
     * @param int $loanId
     *
     * @return array
     */
    public function getLoanAccrued(int $loanId)
    {
        return DB::selectOne(
            '
                SELECT
                    SUM(y.principal)        as accrued_principal,
                    SUM(y.accrued_interest) as accrued_interes,
                    SUM(y.late_interest)    as late_payment_fee
                FROM (
                    (SELECT
                        SUM(ii.principal) AS principal,
                        SUM(ii.accrued_interest) AS accrued_interest,
                        SUM(ii.late_interest) AS late_interest
                    FROM investor_installment AS ii
                    INNER JOIN installment i on ii.installment_id = i.installment_id
                    WHERE ii.loan_id = :loan_id
                    AND ii.paid = :not_payed
                    AND i.payment_status = :scheduled
                    GROUP BY i.installment_id
                    ORDER BY i.installment_id ASC
                    LIMIT 1)
                    UNION
                    (SELECT
                        SUM(ii.principal) AS principal,
                        SUM(ii.accrued_interest) AS accrued_interest,
                        SUM(ii.late_interest) AS late_interest
                    FROM investor_installment AS ii
                    INNER JOIN installment i on ii.installment_id = i.installment_id
                    WHERE ii.loan_id = :loan_id
                    AND ii.paid = :not_payed
                    AND i.payment_status = :late
                    GROUP BY i.installment_id
                    ORDER BY i.installment_id ASC)
                ) AS y
            ',
            [
                'loan_id' => $loanId,
                'not_payed' => InvestorInstallment::INVESTOR_INSTALLMENT_NOT_PAID,
                'scheduled' => Installment::STATUS_SCHEDULED,
                'late' => Installment::STATUS_LATE
            ]
        );
    }

    public function getLoanRepayments(int $loanId)
    {
        return $results = DB::select(
            DB::raw(
                "
                SELECT
                    sum(y.principal)        as repaid_princ,
                    sum(y.accrued_interest) as repaid_interest,
                    sum(y.late_interest)    as late_interes
                FROM
                (
                    SELECT *
                    FROM investor_installment as iii
                    WHERE iii.paid = '" . InvestorInstallment::INVESTOR_INSTALLMENT_PAID_ID . "'
                        AND iii.loan_id = '$loanId'
                        order by (investor_installment_id, 'asc')
                        limit 1
                ) as y
                GROUP BY
                    y.loan_id
            "
            ),
        );
    }

    /**
     * @param $loan
     * @param $firstNotPaidDate
     *
     * @return Loan
     */
    public function updateOverdueDays(
        Loan $loan,
        string $firstNotPaidDate,
        Carbon $nowDate = null
    ) {
        if (null === $nowDate) {
            $nowDate = Carbon::today();
        }

        $installmentDueDate = Carbon::parse($firstNotPaidDate);

        $overdueDays = 0;
        if ($nowDate->gt($installmentDueDate)) {
            $overdueDays = InstallmentCalculator::simpleDateDiff(
                $nowDate,
                $installmentDueDate
            );
        }

        $loan->overdue_days = $overdueDays;
        $loan->save();

        return $loan;
    }

    /**
     * @param array $where
     *
     * @return Builder
     */
    public function getAllCount(array $where = []): Builder
    {
        $builder = DB::table('loan');
        $builder = $builder->select(DB::raw('count(*)'));

        return $builder;
    }

    public function unblockLoans()
    {
        return DB::select(
            DB::raw(
                "
                    UPDATE loan
                    SET blocked = 0,
                        blocked_at = null
                    WHERE blocked = :blocked
                AND NOW() > blocked_at + interval '1 minute'
            "
            ),
            ['blocked' => 1],
        );
    }
}
