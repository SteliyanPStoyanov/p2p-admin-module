<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Modules\Common\Repositories\InstallmentRepository;
use Modules\Common\Repositories\LoanRepository;
use Modules\Common\Services\Loan\WherePipeline\WherePipeline;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\CacheService;
use Throwable;

class LoanService extends BaseService
{
    const KEY_STATUS = 'status';

    private LoanRepository $loanRepository;
    private InstallmentRepository $installmentRepository;
    private CacheService $cacheService;

    public function __construct(
        LoanRepository $loanRepository,
        InstallmentRepository $installmentRepository,
        CacheService $cacheService
    ) {
        $this->loanRepository = $loanRepository;
        $this->installmentRepository = $installmentRepository;
        $this->cacheService = $cacheService;

        parent::__construct();
    }

    /**
     * @param int $loanId
     *
     * @return mixed
     */
    public function getById(int $loanId)
    {
        return $this->loanRepository->getById($loanId);
    }

    /**
     * Get paginated loans according to selected filters
     * Used in:
     *     - /profile/invest (Profile invest page)
     *     - /invest (Public website invest page)
     *
     * @param int $length
     * @param array $whereData
     * @param int $skipLoansOfInvestorId
     * @param array $order
     * @return LengthAwarePaginator
     */
    public function getLoansForSite(
        int $length,
        array $whereData,
        int $skipLoansOfInvestorId = 0, // used for skip already invested in
        array $order = []
    ): LengthAwarePaginator {
        $builder = $this->loanRepository->getAll();

        // prepare limit
        if (!empty($whereData['limit'])) {
            $length = (int)$whereData['limit'];
            unset($whereData['limit']);
        }

        // add order
        if (empty($order) && !empty($whereData['order'])) {
            $order = $this->getOrderConditions($whereData);
            unset($whereData['order']);
        } else {
            $order = [
                'active' => 'DESC',
                'loan_id' => 'DESC'
            ];
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        // add where conditions
        $wrapper = WherePipeline::run($builder, $whereData, $skipLoansOfInvestorId);
        $wrapper->setWhere(
            array_merge(
                [
                    ['loan.status', '=', 'active'],
                    ['loan.unlisted', '=', '0'],
                ],
                $wrapper->getWhere()
            )
        );
        $wrapper->compile();


        // prepare result
        $builder = $wrapper->getBuilder();

        $result = $builder->paginate($length);
        $records = ($result->count() > 0 ? Loan::hydrate($result->all()) : new Collection);
        $result->setCollection($records);

        return $result;
    }

    /**
     * Get paginated loans according to selected filters
     * Used in:
     *     - admin/loans
     *     - admin/re-buying-loans
     *
     * @param int $length
     * @param array $whereData
     * @param bool $rebuying
     * @param array $order
     * @return LengthAwarePaginator
     */
    public function getLoansForAdmin(
        int $length,
        array $whereData,
        bool $rebuying = false,
        array $order = ['loan_id' => 'DESC']
    ): LengthAwarePaginator {
        $builder = $this->loanRepository->getAll();

        // add additional select condition for admin/loans page
        if ($rebuying === false) {
            $builder->addSelect(DB::raw('
                (SELECT COALESCE(SUM(investment.amount), 0) FROM investment WHERE investment.loan_id = loan.loan_id) AS invested_sum,
                (SELECT COALESCE(SUM(investment.percent),0) FROM investment WHERE investment.loan_id = loan.loan_id) AS invested_percent
            '));
        }


        // prepare order
        if (isset($whereData['order'])) {
            $order = $this->getOrderConditions($whereData);
            unset($whereData['order']);
        }
        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }


        // prepare limit
        if (!empty($whereData['limit'])) {
            $length = (int)$whereData['limit'];
            unset($whereData['limit']);
        }


        // if filters contains loan_id -> ignore other filters
        $ignoreFilters = false;
        if (!empty($whereData['loan_id'])) {
            $ignoreFilters = true;
        }


        // prepare where conditions
        $wrapper = WherePipeline::run($builder, $whereData, 0, $ignoreFilters, true);

        $wrapper->setWhere(
            array_merge(
                $wrapper->getWhere(),
                parent::getWhereConditions($wrapper->getData())
            )
        );
        $wrapper->compile();


        $builder = $wrapper->getBuilder();


        // add additional where condition for admin/re-buying-loans page
        if (
            $rebuying === true
            && !array_search(self::KEY_STATUS, array_column($wrapper->getWhere(), 0))
        ) {
            $builder->whereIn('loan.status', Loan::getFinalStatuses());
            $builder->where('loan.unlisted', 1);
        }

        // prepare result
        $result = $builder->paginate($length);
        $records = ($result->count() > 0 ? Loan::hydrate($result->all()) : new Collection);
        $result->setCollection($records);

        return $result;
    }

    /**
     * Get appropriate loan for mass investment(InvestAll, Auto-Invest Strategies)
     *
     * @param array $whereData - filters
     * @param int|null $afterLoanId - if passed will be taken the loan after that ID
     * @param float|null $minAmount
     * @param int $skipLoansOfInvestorId - if passed we will ignore already invested in loans
     *
     * @return Loan|null
     */
    public function getLoanForInvestAll(
        array $whereData,
        int $afterLoanId = null,
        float $minAmount = null,
        int $skipLoansOfInvestorId = 0, // used for skip already invested
        array $order = ['loan_id' => 'ASC'] // important to start from begin
    ): ?Loan
    {
        if (isset($whereData['limit'])) {
            unset($whereData['limit']);
        }

        // unset top limit, since we have lower limit, and if we check both
        // we will lose some loans for buying
        if (!empty($whereData['max_amount'])) {
            unset($whereData['max_amount']);
        }
        if (!empty($whereData['amount_available']['to'])) {
            unset($whereData['amount_available']['to']);
        }


        // reset filter for Manual Invest All, it has only one price and no price range
        if (empty($whereData['amount_available']['from']) && !empty($minAmount)) {
            $whereData['amount_available']['from'] = $minAmount;
        }


        try {
            $builder = $this->loanRepository->getAll();

            // add order
            if (!empty($order)) {
                foreach ($order as $key => $direction) {
                    $builder->orderBy($key, $direction);
                }
            }

            // prepare where conditions
            $whereData['blocked'] = 0; // mandatory condition for invest
            $wrapper = WherePipeline::run($builder, $whereData, $skipLoansOfInvestorId);
            $whereId = [];
            if ($afterLoanId) {
                $whereId[] = ['loan.loan_id', '>', $afterLoanId];
            }
            $wrapper->setWhere(
                array_merge(
                    [
                        ['loan.status', '=', 'active'],
                        ['loan.unlisted', '=', '0'],
                    ],
                    $whereId,
                    $wrapper->getWhere(),
                    parent::getWhereConditions($wrapper->getData())
                )
            );
            $wrapper->compile();


            // prepare result
            $builder = $wrapper->getBuilder();
            $result = $builder->limit(1);


            return (
            $result->count() > 0
                ? (Loan::hydrate([(array)$result->first()]))->first()
                : null
            );
        } catch (Throwable $e) {
            Log::channel('invest_all')->error(
                'LoanService, msg: ' . $e->getMessage() . ", "
                . "file: " . $e->getFile() . ", line: " . $e->getLine()
            );
            return null;
        }
    }

    /**
     * Return loans count accordig to searching criteria
     * Used in:
     *     - profile/auto-invest/create
     *
     * @param array $whereData
     * @param int $investorId
     *
     * @return int
     */
    public function loansCountStrategy(
        array $whereData,
        int $investorId
    ): int {
        if (isset($whereData['limit'])) {
            unset($whereData['limit']);
        }

        $builder = $this->loanRepository->getAllCount($whereData);

        $wrapper = WherePipeline::run($builder, $whereData, $investorId);
        $wrapper->setWhere(
            array_merge(
                [
                    ['loan.status', '=', 'active'],
                    ['loan.unlisted', '=', '0'],
                ],
                $wrapper->getWhere(),
                parent::getWhereConditions($wrapper->getData())
            )
        );

        $builder = $wrapper->compile();

        return $builder->first()->count;
    }

    /**
     * @return Collection
     */
    public function getActiveLoansWithFirstUnpaidInstallment(int $loanId = null)
    {
        return $this->loanRepository->getActiveLoansWithFirstUnpaidInstallment($loanId);
    }

    /**
     * @param Collection $loans
     *
     * @return array
     *
     * @throws ProblemException
     * @throws Throwable
     */
    public function refreshPaymentStatuses(
        Collection $loans,
        Carbon $nowDate = null
    ): array {
        $updatedPaymentStatuses = 0;
        $updatedOverdue = 0;

        if (null === $nowDate) {
            $nowDate = Carbon::today();
        }

        foreach ($loans as $loan) {
            $newStatus = Loan::getPaymentStatusByDate(
                $loan->installment_due_date,
                $nowDate
            );

            // update loan overdue days
            $this->loanRepository->updateOverdueDays(
                $loan,
                $loan->installment_due_date,
                $nowDate
            );
            $updatedOverdue++;

            // skip if loan payment status is not changed(still on same group)
            if (
                $newStatus === $loan->installment_status
                && $newStatus === $loan->payment_status
            ) {
                // we should update payment_status_updated_at,
                // so the next script envoke will skip already checked loans
                $this->loanRepository->refreshLoanPaymentStatusUpdatedAt($loan);
                continue;
            }

            try {
                DB::beginTransaction();

                // update installment
                $newPaymentStatus = $this->getInstallmentPaymentStatusByStatus($newStatus);
                $this->installmentRepository->update(
                    $loan->installment_id,
                    [
                        'status' => $newStatus,
                        'payment_status' => $newPaymentStatus,
                    ]
                );

                // update other installments which due_date is passed
                $this->updateStatusesForLateInstallments(
                    $loan,
                    $loan->installment_id,
                    $nowDate
                );

                // update loan payment status
                $this->loanRepository->changePaymentStatus(
                    $loan,
                    $newStatus
                );

                DB::commit();

                $updatedPaymentStatuses++;
            } catch (Throwable $e) {
                DB::rollBack();
                Log::channel('daily_repayment')->error(
                    'Could not update loan.payment_status, #' . $loan->loan_id
                    . ', old status = ' . $loan->payment_status
                    . ', new status = ' . $newStatus
                    . ', Error: ' . $e->getMessage()
                    . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
                );
                continue;
            }
        }

        return [
            'payment_status' => $updatedPaymentStatuses,
            'overdue_days' => $updatedOverdue,
        ];
    }

    public function updateStatusesForLateInstallments(
        Loan $loan,
        int $installmentId = null,
        Carbon $nowDate = null
    ): int {
        $installments = $loan->getLateInstallments($installmentId);
        if (empty($installments)) {
            return 0;
        }

        $updated = 0;
        foreach ($installments as $installment) {
            $newStatus = Loan::getPaymentStatusByDate(
                $installment->due_date,
                $nowDate
            );

            if ($newStatus === $installment->status) {
                continue;
            }

            $newPaymentStatus = $this->getInstallmentPaymentStatusByStatus($newStatus);

            $this->installmentRepository->update(
                $installment->installment_id,
                [
                    'status' => $newStatus,
                    'payment_status' => $newPaymentStatus,
                ]
            );

            $updated++;
        }

        return $updated;
    }

    public function getInstallmentPaymentStatusByStatus(string $status): string
    {
        if (Loan::PAY_STATUS_CURRENT == $status) {
            return Installment::STATUS_SCHEDULED;
        }

        return Installment::STATUS_LATE;
    }


    /*
     * @param int $maxOverDueDays
     *
     * @return Loan
     */
    public function getLoansForAutoRebuy(int $maxOverDueDays, ?int $loanId)
    {
        $query = DB::table('loan')
            ->select('loan.*')
            ->where(
                [
                    'status' => Loan::STATUS_ACTIVE,
                    'buyback' => 1,
                ]
            );
        if (is_int($loanId)) {
            $query->where('loan_id', '=', $loanId);
        } else {
            $query->whereRaw(
                '(overdue_days > ' . intval(
                    $maxOverDueDays
                ) . ' OR lender_id IN (SELECT ul.lender_id FROM unlisted_loan AS ul WHERE ul.handled = 0))'
            );
        }

        return $query->orderBy('loan_id', 'ASC');
    }

    public function getLoansWithInvestments(int $loanId = null)
    {
        return $this->loanRepository->getLoansWithInvestments($loanId);
    }

    public function recalcInterest(Collection $loans, Carbon $date)
    {
        $updatedLoans = 0;

        foreach ($loans as $loan) {
            try {
                DB::beginTransaction();

                // тука се взима само първa неплатена, НО
                // може да са повече, т.е. ако пич в закъснение 59 дни
                // идва му 3 вноска
                // 1 вата - accrued_interest вече се напълни и стигна до interest, late_interes расте всеки ден
                // 2 рата - accrued_interest вече се напълни и стигна до interest, late_interes расте всеки ден
                // 3та(която идва) - late_interest = 0, accrued_interest расте всеки ден
                // Само инфо: На момента на due_date, accrued_interest == interest

                $firstNotPaidInstallment = $loan->getFirstUnpaidInstallment();

                if ($firstNotPaidInstallment->due_date != $date->format('Y-m-d')) {
                    $dueDate = Carbon::parse($firstNotPaidInstallment->due_date);

                    if ($dueDate->gt($date)) {
                        $this->refreshAccruedInterest($firstNotPaidInstallment, $date);
                    } else {
                        $this->refreshLateInterest($firstNotPaidInstallment, $date);
                    }
                } else {
                    $this->finalizeAccruedInterest($firstNotPaidInstallment);
                }

                $this->loanRepository->refreshInterestUpdatedAt($loan);

                DB::commit();

                $updatedLoans++;
            } catch (Throwable $e) {
                DB::rollBack();
                Log::channel('daily_interest_refresh')->error(
                    $e->getMessage() . ', file: ' . $e->getFile(). ', line: ' . $e->getLine()
                );
            }
        }

        return $updatedLoans;
    }

    public function finalizeAccruedInterest(Installment $installment)
    {
        foreach ($installment->investorInstallments as $investorInstallment) {
            $this->installmentRepository->updateInvestorInstallmentAccruedInterest(
                $investorInstallment,
                $investorInstallment->interest
            );
        }
    }

    public function refreshAccruedInterest(Installment $installment, Carbon $date)
    {
        $dueDate = Carbon::parse($installment->due_date);
        $previousInstallment = $installment->getPreviousInstallment();
        $previousDueDate = $previousInstallment->due_date ?? null;

        if (null !== $previousDueDate) {
            $previousDueDate = Carbon::parse($previousDueDate);

            // if it's a prepaid installment in future, the previous installment
            // should be also in future, so we will null that value,
            // and the investment date will be taken
            if (1 == $previousInstallment->paid && null == $previousInstallment->paid_at) {
                $previousDueDate = null;
            }
        }

        foreach ($installment->investorInstallments as $investorInstallment) {
            $investment = $investorInstallment->investment();
            $investedAt = Carbon::parse($investment->created_at);

            // if there is no previous installment
            if (null !== $previousDueDate && $previousDueDate->gte($investedAt)) {
                $previousDueDate = Carbon::parse($previousDueDate);
            } else {
                $previousDueDate = $investedAt;
            }

            $accruedInterest = InstallmentCalculator::calcAccruedInterest(
                $date,
                $dueDate,
                $previousDueDate,
                $investorInstallment->interest
            );

            $this->installmentRepository->updateInvestorInstallmentAccruedInterest(
                $investorInstallment,
                $accruedInterest
            );
        }
    }

    public function refreshLateInterest(Installment $installment, Carbon $date)
    {
        // just updating installment payment status
        $previousDueDate = $installment->due_date;
        $newPaymentStatus = Loan::getPaymentStatusByDate($previousDueDate);
        $this->installmentRepository->update(
            $installment->installment_id,
            [
                'status' => $newPaymentStatus,
            ]
        );


        $loanInterestRatePercent = $installment->loan->interest_rate_percent;

        // loop through investor installments(on installment level) and update investor installments(late interest)
        foreach ($installment->investorInstallments as $investorInstallment) {
            $investment = $investorInstallment->investment();
            $investedAt = !empty($investment->created_at)
                ? Carbon::parse($investment->created_at)
                : null;
            $dueDate = Carbon::parse($previousDueDate);

            if (null !== $investedAt && $dueDate->lt($investedAt)) {
                $dueDate = $investedAt;
            }

            $lateInterest = InstallmentCalculator::calcLateInterest(
                $date,
                $dueDate,
                $investorInstallment->principal,
                $loanInterestRatePercent
            );

            $this->installmentRepository->updateInvestorInstallmentLateInterest(
                $investorInstallment,
                $lateInterest
            );
        }


        $nextInstallment = $installment->getNextInstallment();
        if (empty($nextInstallment)) {
            return;
        }

        if ($nextInstallment->due_date > $date) {
            $this->refreshAccruedInterest($nextInstallment, $date);
            return;
        }

        return $this->refreshLateInterest($nextInstallment, $date);
    }

    /**
     * @param int $loanId
     *
     * @return array
     */
    public function getAccrued(int $loanId)
    {
        return $this->loanRepository->getLoanAccrued($loanId);
    }

    /**
     * @param int $loanId
     *
     * @return array
     */
    public function getLoanRepayments(int $loanId)
    {
        return $this->loanRepository->getLoanRepayments($loanId);
    }

    /**
     * @return array
     */
    public function getLoansCountries(): array
    {
        $cacheKey = 'get_loans_countries';

        if ($this->cacheService->get($cacheKey) == null) {
            $this->cacheService->set(
                $cacheKey,
                Loan::getLoansCountries(),
                600
            );
        }

        return (array)$this->cacheService->get($cacheKey);
    }

    /**
     * @return array
     */
    public function getLoansOriginators(): array
    {
        $cacheKey = 'get_loans_originators';

        if ($this->cacheService->get($cacheKey) == null) {
            $this->cacheService->set(
                $cacheKey,
                Loan::getLoansOriginators(),
                600
            );
        }

        return (array)$this->cacheService->get($cacheKey);
    }

    public function unblockLoans()
    {
        return $this->loanRepository->unblockLoans();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function getUnlistedLoansWithoutFinalPaymentStatus()
    {
        $query = DB::table('loan')
            ->select('loan.*')
            ->where(
                [
                    'unlisted' => 1,
                    'final_payment_status' => null,
                ]
            );

        return $query->orderBy('loan_id', 'ASC');
    }

    /**
     * @param Loan $loan
     * @param string $paymentStatus
     *
     * @return mixed
     */
    public function updateFinalPaymentStatus(Loan $loan, string $paymentStatus)
    {
        return $this->loanRepository->loanUpdate(
            $loan->loan_id,
            [
                'final_payment_status' => $paymentStatus
            ]
        );
    }

    public function getLoansWithBadInvestedAmount()
    {
        $query = "
        select
            res.loan_id,
            res.amount_afranga,
            res.amount_available,
            res.amount_invested,
            res.percent,
            array_agg(res.reason) as reason
        from (
                select
                    lo.loan_id,
                    lo.amount_afranga,
                    lo.amount_available,
                    sum(i.amount) as amount_invested,
                    round(SUM(i.percent)::decimal, 2) as percent,
                    'sum_of_percents_higher' as reason
                from loan as lo
                join investment i on lo.loan_id = i.loan_id
                where lo.loan_id in (
                        select distinct i.loan_id
                        from investment i
                        group by i.loan_id
                        having round(SUM(i.percent)::decimal, 2) > 90
                ) and lo.unlisted = 0
                group by lo.loan_id
            union
                select
                    lo2.loan_id,
                    lo2.amount_afranga,
                    lo2.amount_available,
                    sum(i2.amount) as amount_invested,
                    round(SUM(i2.percent)::decimal, 2) as percent,
                    'available_amount_less' as reason
                from loan as lo2
                join investment i2 on lo2.loan_id = i2.loan_id
                where lo2.loan_id in (
                    select l3.loan_id
                    from loan l3
                    join (
                        select
                            i.loan_id,
                            SUM(amount) as amount_invested
                        from investment i
                        where
                            i.active = 1
                            and i.deleted = 0
                        group by i.loan_id
                    ) as lia on (
                        lia.loan_id = l3.loan_id
                        and (lia.amount_invested - round((l3.amount_afranga - (l3.amount_afranga * 10 / 100))::numeric, 2)) >= 1
                    )
                ) and lo2.unlisted = 0
                group by lo2.loan_id
            union
                select
                    lo3.loan_id,
                    lo3.amount_afranga,
                    lo3.amount_available,
                    sum(i3.amount) as amount_invested,
                    round(SUM(i3.percent)::decimal, 2) as percent,
                    'sum_of_percents_higher' as reason
                from loan as lo3
                join investment i3 on lo3.loan_id = i3.loan_id
                where lo3.loan_id in (
                    select l2.loan_id
                    from loan l2
                    where
                        l2.unlisted = 0
                        and l2.amount_afranga = l2.amount_available
                ) and lo3.unlisted = 0
                group by lo3.loan_id
        ) as res
        group by
            res.loan_id,
            res.amount_afranga,
            res.amount_available,
            res.amount_invested,
            res.percent
        order by res.loan_id ASC
        ";

        return DB::select(
            DB::raw($query)
        );
    }

    public function getBuilderForInvestAll(
        array $whereData,
        int $afterLoanId = null,
        float $minAmount = null,
        int $skipLoansOfInvestorId = 0, // used for skip already invested
        array $order = ['loan_id' => 'ASC'] // important to start from begin
    )
    {
        if (isset($whereData['limit'])) {
            unset($whereData['limit']);
        }

        // unset top limit, since we have lower limit, and if we check both
        // we will lose some loans for buying
        if (!empty($whereData['max_amount'])) {
            unset($whereData['max_amount']);
        }
        if (!empty($whereData['amount_available']['to'])) {
            unset($whereData['amount_available']['to']);
        }


        // reset filter for Manual Invest All, it has only one price and no price range
        if (empty($whereData['amount_available']['from']) && !empty($minAmount)) {
            $whereData['amount_available']['from'] = $minAmount;
        }


        try {
            $builder = $this->loanRepository->getAll('loan.loan_id');

            // add order
            if (!empty($order)) {
                foreach ($order as $key => $direction) {
                    $builder->orderBy($key, $direction);
                }
            }

            // prepare where conditions
            $whereData['blocked'] = 0; // mandatory condition for invest
            $wrapper = WherePipeline::run($builder, $whereData, $skipLoansOfInvestorId);
            $whereId = [];
            if ($afterLoanId) {
                $whereId[] = ['loan.loan_id', '>', $afterLoanId];
            }
            $wrapper->setWhere(
                array_merge(
                    [
                        ['loan.status', '=', 'active'],
                        ['loan.unlisted', '=', '0'],
                    ],
                    $whereId,
                    $wrapper->getWhere(),
                    parent::getWhereConditions($wrapper->getData())
                )
            );
            $wrapper->compile();


            return $wrapper->getBuilder();

        } catch(Throwable $e) {
            Log::channel('invest_all')->error(
                'LoanService->getBuilderForInvestAll(), '
                . 'msg: ' . $e->getMessage() . ", "
                . "file: " . $e->getFile() . ", "
                . "line: " . $e->getLine()
            );
            return null;
        }
    }

    public function getBlockedLoansByIds(array $lonaIds)
    {
        return Loan::whereIn('loan_id', $lonaIds)
            ->lockForUpdate()
            ->get()
            ->all();
    }
}
