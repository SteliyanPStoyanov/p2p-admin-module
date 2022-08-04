<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Modules\Common\Entities\Loan;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Portfolio extends BaseModel implements LoggerInterface
{
    public const PORTFOLIO_TYPE_QUALITY = 'quality';
    public const PORTFOLIO_TYPE_MATURITY = 'maturity';

    public const PORTFOLIO_RANGE_1 = 'range1';
    public const PORTFOLIO_RANGE_2 = 'range2';
    public const PORTFOLIO_RANGE_3 = 'range3';
    public const PORTFOLIO_RANGE_4 = 'range4';
    public const PORTFOLIO_RANGE_5 = 'range5';

    public const QUALITY_CURRENT = Loan::PAY_STATUS_CURRENT;
    public const QUALITY_1_15_DAYS_LATE = Loan::PAY_STATUS_1_15;
    public const QUALITY_16_30_DAYS_LATE = Loan::PAY_STATUS_16_30;
    public const QUALITY_31_60_DAYS = Loan::PAY_STATUS_31_60;
    public const QUALITY_LATE = Loan::PAY_STATUS_LATE;

    public const MATURITY_1_3_MONTHS = '1 – 3 months';
    public const MATURITY_4_6_MONTHS = '4 – 6 months';
    public const MATURITY_7_12_MONTHS = '7 – 12 months';
    public const MATURITY_12_PLUS_MONTHS = '12+ months';
    public const MATURITY_LATE = 'late';

    public const CHARTS_MATURITY_1_3_MONTHS = '1 – 3 mos.';
    public const CHARTS_MATURITY_4_6_MONTHS = '4 – 6 mos.';
    public const CHARTS_MATURITY_7_12_MONTHS = '7 – 12 mos.';
    public const CHARTS_MATURITY_12_PLUS_MONTHS = '12 - 24 mos.';
    public const CHARTS_MATURITY_LATE = '24+ mos.';

    public const RANGE_STRING = 'range';

    /**
     * @var string
     */
    protected $table = 'portfolio';

    /**
     * @var string
     */
    protected $primaryKey = 'portfolio_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'portfolio_id',
        'active',
        'deleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'enabled_at',
        'enabled_by',
        'disabled_at',
        'disabled_by',
    ];

    /**
     * @return array
     */
    public static function getPortfolioTypes(): array
    {
        return [
            self::PORTFOLIO_TYPE_QUALITY,
            self::PORTFOLIO_TYPE_MATURITY,
        ];
    }

    /**
     * @param string $type
     * @param string $range
     * @return string|null
     */
    public static function getPortfolioMapping(string $type, string $range): ?string
    {
        if ($type == 'quality') {
            $mapping = self::getQualityMapping($range);
        } else {
            $mapping = self::getMaturityMapping($range);
        }

        return $mapping;
    }

    /**
     * @return array
     */
    public static function getMaturityStatuses(): array
    {
        return [
            self::PORTFOLIO_RANGE_1,
            self::PORTFOLIO_RANGE_2,
            self::PORTFOLIO_RANGE_3,
            self::PORTFOLIO_RANGE_4,
            self::PORTFOLIO_RANGE_5
        ];
    }

    /**
     * @param string $range
     * @param bool $viceVersa
     * @return string
     */
    public static function getMaturityChartsMapping(
        string $range,
        bool $viceVersa = false
    ): ?string {
        $mapping = [
            self::PORTFOLIO_RANGE_1 => self::CHARTS_MATURITY_1_3_MONTHS,
            self::PORTFOLIO_RANGE_2 => self::CHARTS_MATURITY_4_6_MONTHS,
            self::PORTFOLIO_RANGE_3 => self::CHARTS_MATURITY_7_12_MONTHS,
            self::PORTFOLIO_RANGE_4 => self::CHARTS_MATURITY_12_PLUS_MONTHS,
            self::PORTFOLIO_RANGE_5 => self::CHARTS_MATURITY_LATE,
        ];

        if ($viceVersa) {
            $mapping = array_flip($mapping);
        }

        if (empty($mapping[$range])) {
            return null;
        }

        return $mapping[$range];
    }

    /**
     * @param string $range
     * @param bool $viceVersa
     * @return string
     */
    public static function getQualityMapping(
        string $range,
        bool $viceVersa = false
    ): ?string {
        $mapping = [
            self::PORTFOLIO_RANGE_1 => self::QUALITY_CURRENT,
            self::PORTFOLIO_RANGE_2 => self::QUALITY_1_15_DAYS_LATE,
            self::PORTFOLIO_RANGE_3 => self::QUALITY_16_30_DAYS_LATE,
            self::PORTFOLIO_RANGE_4 => self::QUALITY_31_60_DAYS,
            self::PORTFOLIO_RANGE_5 => self::QUALITY_LATE,
        ];
        if ($viceVersa) {
            $mapping = array_flip($mapping);
        }

        if (empty($mapping[$range])) {
            return null;
        }

        return $mapping[$range];
    }

    /**
     * @param string $range
     * @param bool $viceVersa
     * @return string
     */
    public static function getMaturityMapping(
        string $range,
        bool $viceVersa = false
    ): ?string {
        $mapping = [
            self::PORTFOLIO_RANGE_1 => self::MATURITY_1_3_MONTHS,
            self::PORTFOLIO_RANGE_2 => self::MATURITY_4_6_MONTHS,
            self::PORTFOLIO_RANGE_3 => self::MATURITY_7_12_MONTHS,
            self::PORTFOLIO_RANGE_4 => self::MATURITY_12_PLUS_MONTHS,
            self::PORTFOLIO_RANGE_5 => self::MATURITY_LATE,
        ];

        if ($viceVersa) {
            $mapping = array_flip($mapping);
        }

        if (empty($mapping[$range])) {
            return null;
        }

        return $mapping[$range];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        )->orderByDesc('portfolio_id');
    }

    /**
     * [getRanges description]
     *
     * @param string $mapType
     *
     * @return array
     */
    public function getRanges(): array
    {
        return [
            self::getPortfolioMapping($this->type, 'range1') => $this->range1,
            self::getPortfolioMapping($this->type, 'range2') => $this->range2,
            self::getPortfolioMapping($this->type, 'range3') => $this->range3,
            self::getPortfolioMapping($this->type, 'range4') => $this->range4,
            self::getPortfolioMapping($this->type, 'range5') => $this->range5,
        ];
    }

    /**
     * Get maturity column -> rangeX by status
     *
     * @param Carbon $date
     *
     * @return string = rangeX
     */
    public static function getMaturityRangeColumnByDate(
        Carbon $date,
        Carbon $fromDate = null
    ): string {
        $fromDate = (null === $fromDate) ? Carbon::now() : $fromDate;

        if ($fromDate->gt($date)) {
            return self::getMaturityMapping(self::MATURITY_1_3_MONTHS, true); // now we use first range
            // return self::getMaturityMapping(self::MATURITY_LATE, true); // before we handled overdued as late
        }


        $days = InstallmentCalculator::simpleDateDiff(
            $fromDate,
            $date
        );
        $maturityStatus = self::getMaturityRanges($days);
        return self::getMaturityMapping($maturityStatus, true);
    }

    /**
     * [getMaturityRanges description]
     *
     * @param int $daysCount
     *
     * @return string
     */
    public static function getMaturityRanges(int $daysCount): string
    {
        if ($daysCount < 4 * 30) {
            return self::MATURITY_1_3_MONTHS;
        }

        if ($daysCount >= 4 * 30 && $daysCount < 6 * 30) {
            return self::MATURITY_4_6_MONTHS;
        }

        if ($daysCount >= 6 * 30 && $daysCount <= 12 * 30) {
            return self::MATURITY_7_12_MONTHS;
        }

        return self::MATURITY_12_PLUS_MONTHS;
    }

    public function updateQualityOnPayment(
        Installment $currentInstallment,
        Installment $nextInstallment = null
    ) {
        $this->reduceQualityByPaymentStatus($currentInstallment->status, false);

        if ($nextInstallment) {
            $nextRange = self::getQualityMapping($nextInstallment->status, true);
            $this->{$nextRange} = $this->{$nextRange} + 1;
        }

        $this->save();
    }

    public function reduceMaturitByFinalPaymentDate(
        Carbon $loanFinalPaymentDate,
        Carbon $now
    ) {
        $range = self::getMaturityRangeColumnByDate($loanFinalPaymentDate, $now);
        $this->{$range} = $this->{$range} - 1;
        $this->save();
    }

    public function reduceQualityByPaymentStatus(
        string $paymentStatus,
        bool $save = true
    ) {
        $currentRange = self::getQualityMapping($paymentStatus, true);
        $this->{$currentRange} = $this->{$currentRange} - 1;

        if ($save) {
            $this->save();
        }
    }
}
