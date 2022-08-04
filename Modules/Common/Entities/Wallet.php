<?php

namespace Modules\Common\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Common\Observers\WalletObserver;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Wallet extends BaseModel implements LoggerInterface
{
    public const INVESTOR_WALLET_ID = 1;
    /**
     * @var string
     */
    protected $table = 'wallet';

    /**
     * @var string
     */
    protected $primaryKey = 'wallet_id';

    /**
     * @var string[]
     */
    protected $guarded = [
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

    public static function boot()
    {
        // Important! DO NOT switch places with parent boot and self observe. It will cause error
        parent::boot();

        self::observe(WalletObserver::class);
    }

    public function hasUninvestedAmount(float $amount): bool
    {
        return ($this->uninvested >= $amount);
    }

    public function getMaxCountToBuy(float $amount): int
    {
        return (floor(($this->uninvested/$amount)) + 1);
    }

    /**
     * [invest description]
     * @param float $amount
     * @return bool
     */
    public function invest(float $amount): bool
    {
        if (!$this->hasUninvestedAmount($amount)) {
            return false;
        }

        $this->actualizeAmountsForInvestment($amount);
        $this->save();

        return true;
    }

    public function actualizeAmountsForInvestment(float $amount)
    {
        $this->uninvested = $this->uninvested - $amount;
        $this->invested = $this->invested + $amount;
    }

    public function actualizeAmountsForSale(float $amount)
    {
        $this->uninvested = $this->uninvested + $amount;
        $this->invested = $this->invested - $amount;
    }

    public function actualizeAmountForSecondaryMarketSeller(float $price, float $premium)
    {
        $this->uninvested = $this->uninvested + $price + $premium;
        $this->invested = $this->invested - $price;
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
        );
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function addFunds($amount)
    {
        $this->deposit += $amount;
        $this->uninvested += $amount;

        $this->save();

        return $this;
    }

    public function addIncomeForInstallment(
        InvestorInstallment $investorInstallment
    ): void
    {
        $this->income = $this->income + $investorInstallment->getInstallmentIncome();

        $this->interest = $this->interest + $investorInstallment->getInstallmentInterest();

        if ($investorInstallment->late_interest > 0) {
            $this->late_interest = $this->late_interest + $investorInstallment->late_interest;
        }

        $this->invested = $this->invested - $investorInstallment->principal;

        $this->uninvested = $this->uninvested
            + $investorInstallment->getInstallmentIncome()
            + $investorInstallment->principal;

        $this->save();
    }

    public function addIncomeAmounts(
        float $principalAmount,
        float $interestAmount,
        float $lateInterestAmount = 0.00
    ): void
    {
        $interestTotal = ($interestAmount + $lateInterestAmount);

        $this->income = $this->income + $interestTotal;

        $this->interest = $this->interest + $interestAmount;

        if ($lateInterestAmount > 0) {
            $this->late_interest = $this->late_interest + $lateInterestAmount;
        }

        $this->invested = $this->invested - $principalAmount;

        $this->uninvested = $this->uninvested
            + $interestTotal
            + $principalAmount;

        $this->save();
    }

    private function getPrincipalAmount(
        InvestorInstallment $investorInstallment,
        array $unpaidInstallments = []
    ): float
    {
        if (empty($unpaidInstallments)) {
            return $investorInstallment->principal;
        }

        $sum = 0;
        foreach ($unpaidInstallments as $key => $unpaidInstallment) {
            $sum += $unpaidInstallment->principal;
        }

        return $sum;
    }

    /**
     * @param int $currencyId
     *
     * @return array
     */
    public static function sumWallet(int $currencyId = Currency::ID_EUR): array
    {
        $results = DB::select(
            DB::raw(
                "
            SELECT
                 sum(invested) as invested,
                 (sum(uninvested) + sum(blocked_amount)) as uninvested,
                 sum(total_amount) as total_amount
            FROM wallet
            WHERE currency_id = " . $currencyId . "
        "
            )
        );
        $results = array_map(
            function ($value) {
                return (array)$value;
            },
            $results
        );

        return current($results);
    }

    /**
     * @param int $currencyId
     * @param int $investorId
     *
     * @return mixed
     */
    public static function sumWalletByInvestor(int $currencyId = Currency::ID_EUR, int $investorId)
    {
        $results = DB::select(
            DB::raw(
                "
            SELECT
                 (sum(invested) + sum(uninvested)) as balance
            FROM wallet
            WHERE currency_id = " . $currencyId . "
            AND investor_id = " . $investorId . "
        "
            )
        );


        return current($results);
    }


    /**
     * @param int $currencyId
     * @param int $investorId
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    public function getWalletForDates(
        int $currencyId = Currency::ID_EUR,
        int $investorId
    )
    {
        return
            $this::selectRaw(
                'uninvested'
            )->where(
                [
                    'currency_id' => $currencyId,
                    'investor_id' => $investorId,
                ]
            )->first()->toArray();
    }

    /**
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->uninvested + $this->blocked_amount;
    }

    /**
     * @return bool
     */
    public function hasDeposit()
    {
        return $this->deposit > 0;
    }
}
