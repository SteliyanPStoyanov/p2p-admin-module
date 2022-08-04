<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorQualityRange;
use Modules\Common\Entities\InvestorQualityRangeHistory;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Repositories\PortfolioRepository;
use Modules\Core\Database\Collections\CustomEloquentCollection;
use Modules\Core\Services\BaseService;
use Throwable;

class PortfolioService extends BaseService
{
    private PortfolioRepository $portfolioRepository;

    /**
     * @param PortfolioRepository $portfolioRepository
     */
    public function __construct(
        PortfolioRepository $portfolioRepository
    ) {
        $this->portfolioRepository = $portfolioRepository;

        parent::__construct();
    }

    /**
     * @param int $investorId
     *
     * @return mixed
     */
    public function getByInvestorId(int $investorId)
    {
        return $this->portfolioRepository->getByInvestorId($investorId);
    }

    /**
     * @param int    $investorId
     * @param int    $currencyId
     * @param string $type
     *
     * @return array
     */
    public function getPortfolio(
        int $investorId,
        int $currencyId = Currency::ID_EUR
    ): array
    {
        return [
            Portfolio::PORTFOLIO_TYPE_QUALITY => $this->getQuality(
                $investorId,
                $currencyId
            )->getRanges(),
            Portfolio::PORTFOLIO_TYPE_MATURITY => $this->getMaturity(
                $investorId,
                $currencyId
            )->getRanges(),
        ];
    }

    public function getQuality(
        int $investorId,
        int $currencyId = Currency::ID_EUR
    )
    {
        return $this->portfolioRepository->getPortfolio(
            $investorId,
            Portfolio::PORTFOLIO_TYPE_QUALITY,
            $currencyId
        );
    }

    public function getMaturity(
        int $investorId,
        int $currencyId = Currency::ID_EUR
    )
    {
        return $this->portfolioRepository->getPortfolio(
            $investorId,
            Portfolio::PORTFOLIO_TYPE_MATURITY,
            $currencyId
        );
    }

    public function getQualityRangeByPaymentStatus(
        string $paymentStatus
    ): string
    {
        return Portfolio::getQualityMapping(
            $paymentStatus,
            true
        );
    }

    public function getRangeNumberFromRange(
        string $qualityRange
    ): int
    {
        return (int) str_replace(
            Portfolio::RANGE_STRING,
            '',
            $qualityRange
        );
    }

    public function addInvestorQualityRange(
        int $investorId,
        int $loanId,
        int $rangeNumber
    ): InvestorQualityRange
    {
        $investorQualityRange = new InvestorQualityRange();
        $investorQualityRange->fill([
            'investor_id' => $investorId,
            'loan_id' => $loanId,
            'range' =>  $rangeNumber,
        ]);
        $investorQualityRange->save();

        return $investorQualityRange;
    }

    public function getInvestorRangeCount(
        int $investorId,
        int $qualityRangeNumber
    ): int
    {
        return InvestorQualityRange::where([
            'investor_id' => $investorId,
            'range' => $qualityRangeNumber,
        ])->count();
    }

    public function updatePortfolio(int $investorId, string $type, array $data)
    {
        Portfolio::where([
            'investor_id' => $investorId,
            'type' => $type,
        ])->update($data);

        return true;
    }

    public function massUpdatePortfolio(
        Collection $investorIds,
        int $currencyId,
        string $type,
        string $rangeIncrease = null,
        string $rangeDecrease = null
    ): bool
    {
        if (null === $rangeIncrease && null === $rangeDecrease) {
            return false;
        }

        $update = [];
        if ($rangeIncrease) {
            $update[$rangeIncrease] = DB::raw($rangeIncrease . ' + 1');
        }
        if ($rangeDecrease) {
            $update[$rangeDecrease] = DB::raw($rangeDecrease . ' - 1');
        }

        Portfolio::where('type', '=', $type)
            ->whereIn('investor_id', $investorIds)
            ->update($update);

        return true;
    }

    public function massReduceRangeInPortfolio(
        Collection $investorIds,
        int $currencyId,
        string $type,
        string $rangeDecrease
    ): bool
    {
        return $this->massUpdatePortfolio(
            $investorIds,
            $currencyId,
            $type,
            null,
            $rangeDecrease
        );
    }

    public function massUpdateInvestorQualityRange(
        int $loanId,
        int $newRangeNumber
    ): bool
    {
        InvestorQualityRange::where('loan_id', '=', $loanId)
            ->update([
                'range' => $newRangeNumber,
            ]);

        return true;
    }

    public function massUpdateQualityRange(
        int $loanId,
        int $currencyId,
        string $oldPaymentStatus,
        string $newPaymentStatus
    ): bool
    {
        $qualityRanges = $this->getInvestorQualityRangeRecordsForLoan(
            $loanId
        );
        if ($qualityRanges->count() < 1) {
            return false;
        }

        DB::beginTransaction();

        try {

            $oldRange = Portfolio::getQualityMapping($oldPaymentStatus, true);
            $newRange = Portfolio::getQualityMapping($newPaymentStatus, true);

            $newRangeNumber = $this->getRangeNumberFromRange($newRange);

            // save history for old range
            $this->addInvestorQualityHistoryForRecords($qualityRanges);

            // update quality in portfolio
            $this->massUpdatePortfolio(
                $qualityRanges->pluck('investor_id'),
                $currencyId,
                Portfolio::PORTFOLIO_TYPE_QUALITY,
                $newRange,
                $oldRange
            );

            // update investor quality range
            $this->massUpdateInvestorQualityRange(
                $loanId,
                $newRangeNumber
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            return false;
        }

        return true;
    }

    public function massReduceQualityRange(
        int $loanId,
        int $currencyId,
        string $oldPaymentStatus
    ): bool
    {
        // DB::beginTransaction();

        try {

            $qualityRanges = $this->getInvestorQualityRangeRecordsForLoan(
                $loanId
            );
            if ($qualityRanges->count() < 1) {
                return false;
            }


            $oldRange = Portfolio::getQualityMapping($oldPaymentStatus, true);
            $oldRangeNumber = $this->getRangeNumberFromRange($oldRange);

            // save history for old range
            $this->addInvestorQualityHistoryForRecords($qualityRanges);

            // reduce quality in portfolio
            $this->massReduceRangeInPortfolio(
                $qualityRanges->pluck('investor_id'),
                $currencyId,
                Portfolio::PORTFOLIO_TYPE_QUALITY,
                $oldRange
            );

            // delete records from investor quality range
            $this->massDeleteInvestorQualityRange($loanId);

            // DB::commit();
        } catch (Throwable $e) {
            // DB::rollback();
            return false;
        }

        return true;
    }

    public function massReduceMaturityRange(
        int $loanId,
        Carbon $finalPaymentDay,
        Carbon $repaymentDate,
        int $currencyId = null
    ): bool
    {
        try {
            // detect range for reducing
            $range = Portfolio::getMaturityRangeColumnByDate($finalPaymentDay, $repaymentDate);

            // get distinct investor_id who invest in loan_id
            $subQuery = 'SELECT DISTINCT i.investor_id FROM investment i WHERE i.loan_id = ' . $loanId;
            $investors = Investor::whereRaw('investor_id IN (' . $subQuery . ')')->get()->all();

            foreach ($investors as $investor) {
                $maturity = $investor->maturity();

                if (
                    isset($maturity->{$range})
                    && intval($maturity->{$range}) > 0
                ) {
                    $maturity->{$range} = intval($maturity->{$range}) - 1;
                    $maturity->save();
                }
            }
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    public function massDeleteInvestorQualityRange(int $loanId)
    {
        return DB::delete('DELETE FROM investor_quality_range WHERE loan_id = ' . $loanId);
    }

    public function getQualityRange(string $paymentStatus): string
    {
        // Get range like range1, range2, etc
        $qualityRange = $this->getQualityRangeByPaymentStatus($paymentStatus);
        if (empty($qualityRange)) {
            return '';
        }

        return $qualityRange;
    }

    public function getQualityRangeNumber(string $paymentStatus): int
    {
        // Get range like range1, range2, etc
        $qualityRange = $this->getQualityRangeByPaymentStatus($paymentStatus);
        if (empty($qualityRange)) {
            return 0;
        }

        // Get the number only: range1 -> 1
        $qualityRangeNumber = $this->getRangeNumberFromRange(
            $qualityRange
        );
        if (empty($qualityRangeNumber)) {
            return 0;
        }

        return (int) $qualityRangeNumber;
    }

    public function updateQualityRange(
        int $loanId,
        int $investorId,
        string $paymentStatus
    ): bool
    {
        $qualityRangeNumber = $this->getQualityRangeNumber($paymentStatus);
        if (empty($qualityRangeNumber)) {
            return false;
        }

        DB::beginTransaction();

        try {

            // new investor quality range
            $this->addInvestorQualityRange(
                $investorId,
                $loanId,
                $qualityRangeNumber
            );

            // update the portfolio quality type with the count
            $this-> updatePortfolio(
                $investorId,
                Portfolio::PORTFOLIO_TYPE_QUALITY,
                [
                    $qualityRange => $this->getInvestorRangeCount(
                        $investorId,
                        $qualityRangeNumber
                    ),
                ]
            );


            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            return false;
        }

        return true;
    }

    public function getMaturityRange(
        string $finalDueDate,
        string $createdAt = null
    ): string
    {
        return Portfolio::getMaturityRangeColumnByDate(
            Carbon::parse($finalDueDate),
            (!empty($createdAt) ? Carbon::parse($createdAt) : null)
        );
    }

    public function getMaturityRangeNumber(
        string $finalDueDate,
        string $createdAt = null
    ): int
    {
        $range = $this->getMaturityRange($finalDueDate, $createdAt);

        // Get the number only: range1 -> 1
        $qualityRangeNumber = $this->getRangeNumberFromRange(
            $range
        );
        if (empty($qualityRangeNumber)) {
            return 0;
        }

        return (int) $qualityRangeNumber;
    }

    public function updateMaturityRange(
        int $investorId,
        int $currencyId,
        string $finalDueDate,
        string $createdAt = null
    ): bool
    {
        $rangeMaturity = $this->getMaturityRange($finalDueDate, $createdAt);
        if (empty($rangeMaturity)) {
            return false;
        }

        $maturity = $this->getMaturity($investorId, $currencyId);
        $maturity->increment($rangeMaturity);
        return true;
    }

    public function getInvestorQualityRangeRecordsForLoan(
        int $loanId
    )
    {
        return InvestorQualityRange::where(
            'loan_id',
            $loanId
        )->get();
    }

    public function addInvestorQualityHistory(
        array $data
    ): InvestorQualityRangeHistory
    {
        $historyQualityRange = new InvestorQualityRangeHistory();
        $historyQualityRange->fill($data);
        $historyQualityRange->archived_at = Carbon::now();
        $historyQualityRange->archived_by = Administrator::SYSTEM_ADMINISTRATOR_ID;
        $historyQualityRange->save();

        return $historyQualityRange;
    }

    public function addInvestorQualityHistoryForRecords(
        CustomEloquentCollection $qualityRanges
    )
    {
        $history = [];

        foreach ($qualityRanges as $qualityRange) {
            $history[] = [
                'investor_quality_range_id' => $qualityRange->investor_quality_range_id,
                'investor_id' => $qualityRange->investor_id,
                'loan_id' => $qualityRange->loan_id,
                'range' => $qualityRange->range,
                'active' => $qualityRange->active,
                'created_at' => $qualityRange->created_at,
                'created_by' => $qualityRange->created_by,
                'updated_at' => $qualityRange->updated_at,
                'updated_by' => $qualityRange->updated_by,
                'deleted_at' => $qualityRange->deleted_at,
                'deleted_by' => $qualityRange->deleted_by,
                'enabled_at' => $qualityRange->enabled_at,
                'enabled_by' => $qualityRange->enabled_by,
                'disabled_at' => $qualityRange->disabled_at,
                'disabled_by' => $qualityRange->disabled_by,
                'archived_at' => Carbon::now(),
                'archived_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ];
        }

        return InvestorQualityRangeHistory::insert($history);
    }

    /**
     * @param null|int $portfolioId
     *
     * @return array
     */
    public function getPortfoliosWithMaturityRanges(?int $portfolioId)
    {
        $ranges = [
            'range1' => [
                'from' => Carbon::today(),
                'to' => Carbon::today()->addDays(4 * 30 - 1),
            ],
            'range2' => [
                'from' => Carbon::today()->addDays(4 * 30),
                'to' => Carbon::today()->addDays(6 * 30 - 1),
            ],
            'range3' => [
                'from' => Carbon::today()->addDays(6 * 30),
                'to' => Carbon::today()->addDays(12 * 30),
            ],
            'range4' => [
                'from' => Carbon::today()->addDays(12 * 30),
            ],
        ];

        return $this->portfolioRepository->getActualMaturities($ranges, $portfolioId);
    }

    /**
     * @param array $portfolios
     *
     * @return int
     */
    public function massUpdatePortfolios(array $portfolios): int
    {
        $updatedPortfolios = 0;

        foreach ($portfolios as $portfolio) {
            if ($this->portfolioRepository->updateMaturityRanges($portfolio)) {
                $updatedPortfolios++;
            }
        }

        return $updatedPortfolios;
    }

    /**
     * @param int $investorId
     * @param int $currencyId
     */
    public function addNewInvestorPortfolio(int $investorId, $currencyId = Currency::ID_EUR)
    {

        $data = [
            'investor_id' => $investorId,
            'currency_id' => $currencyId,
            'type' => Portfolio::PORTFOLIO_TYPE_MATURITY,
            'date' => Carbon::now()
        ];

        $this->portfolioRepository->createPortfolio($data);
    }
}
