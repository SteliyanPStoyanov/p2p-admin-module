<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Observers\InvestStrategyObserver;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class InvestStrategy extends BaseModel implements LoggerInterface
{
    protected $table = 'invest_strategy';

    protected $historyClass = InvestStrategyHistory::class;

    protected $primaryKey = 'invest_strategy_id';

    protected $fillable = [
        "investor_id",
        "wallet_id",
        "name",
        "priority",
        "min_amount",
        "max_amount",
        "min_interest_rate",
        "max_interest_rate",
        "min_loan_period",
        "max_loan_period",
        "loan_type",
        "loan_payment_status",
        "portfolio_size",
        "max_portfolio_size",
        "reinvest",
        "include_invested",
        "agreed",
        "active",
        "deleted",
        "total_received",
        "total_invested",
    ];

    public $activating = 0; // used in AutoInvestController - flag for manual activating

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(InvestStrategyObserver::class);
    }

    /**
     * @return BelongsTo
     */
    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    public function wallet()
    {
        return $this->belongsTo(
            Wallet::class,
            'wallet_id',
            'wallet_id'
        );
    }

    /**
     * @return HasMany
     */
    public function investmentBunches(): HasMany
    {
        return $this->hasMany(
            InvestmentBunch::class,
            'invest_strategy_id',
            'invest_strategy_id'
        );
    }

    /**
     * @return mixed
     */
    public function getPrev()
    {
        return InvestStrategy::select(DB::raw('*'))
            ->where('priority', '<', $this->priority)
            ->where('investor_id', '=', $this->investor_id)
            ->where('agreed', '=', 1)
            ->where('deleted', '=', 0)
            ->orderBy('priority', 'desc')
            ->first();
    }

    public function getNext()
    {
        return InvestStrategy::select(DB::raw('*'))
            ->where('priority', '>', $this->priority)
            ->where('investor_id', '=', $this->investor_id)
            ->where('agreed', '=', 1)
            ->where('deleted', '=', 0)
            ->orderBy('priority', 'asc')
            ->first();
    }

    public function updateAmounts(float $repaidPrincipal)
    {
        $this->total_received = $this->total_received + $repaidPrincipal;
        if (1 == $this->reinvest) {
            $this->portfolio_size = $this->portfolio_size - $repaidPrincipal;
        }
        $this->save();
    }

    public function getAvailableAmountForInvestments(): float
    {
        $diff = $this->max_portfolio_size - $this->portfolio_size;
        if ($diff > 0) {
            return $diff;
        }

        return 0;
    }

    public function getMaxPossibleInvestmentsCount(float $amount = null): int
    {
        $total = $this->getAvailableAmountForInvestments();
        if (0 === $total) {
            return 0;
        }

        $checkAmount = (!empty($amount) ? $amount : $this->min_amount);

        return floor(($total / $checkAmount)) + 1;
    }

    public function openForInvesting(float $amount): bool
    {
        // if no limit, we are always open
        if (null === $this->max_portfolio_size) {
            return true;
        }

        $diff = $this->getUninvestedAmount();
        if ($diff <= 0) {
            return false;
        }

        return ($diff >= $amount);
    }

    public function getUninvestedAmount(): float
    {
        $border = (empty($this->max_portfolio_size) ? 0 : (float) $this->max_portfolio_size);
        $current = (empty($this->portfolio_size) ? 0 : (float) $this->portfolio_size);

        if (!empty($border)) {
            return ($border - $current);
        }

        return 0;
    }

    public function stopAllBunches($msg = 'stopAllBunches')
    {
        $investor = $this->investor()->first();

        // we should release running bunch if it's related to strategy which has been stopped
        if (!empty($investor->running_bunch_id)) {
            $bunch = InvestmentBunch::where(
                'investment_bunch_id',
                $investor->running_bunch_id
            )->first();

            if (
                !empty($bunch->investment_bunch_id)
                && $bunch->invest_strategy_id == $this->invest_strategy_id
            ) {
                $investor->removeRunningBunchId();
            }
        }

        return InvestmentBunch::where(
            function ($q) {
                $q->where([
                    ['invest_strategy_id', '=', $this->invest_strategy_id],
                    ['finished', '=', 0],
                ]);
            }
        )->update([
            'finished' => '1',
            'details' => $msg,
        ]);
    }

    // TODO: use when listing autoinvest strategies
    public function getNotReinvestedOuststandingAmount()
    {
        $total = $this->portfolio_size;
        if (empty($total)) {
            $total = 0;
        }

        $totalRecieved = $this->total_received;
        if (empty($total)) {
            $total = 0;
        }

        return ($total - $totalRecieved);
    }

    public function isAgreed(): bool
    {
        return (1 == $this->agreed);
    }

    public function isReinvesting(): bool
    {
        return (1 == $this->reinvest);
    }

    public function isFull(): bool
    {
        return ($this->getAvailableAmountForInvestments() <= 0);
    }

    public static function getDistinctActivePriorities(int $afterPriority = 7)
    {
        return DB::select(
            DB::raw(
                "
            select DISTINCT(priority)
            from invest_strategy is2
            join wallet w2 on w2.investor_id = is2.investor_id
            where
                is2.active = 1
                and is2.agreed = 1
                and is2.deleted = 0
                and is2.portfolio_size < max_portfolio_size
                and w2.uninvested > 10
                and priority > " . $afterPriority . "
            order by priority asc
        "
            )
        );
    }

    public function hasActiveBunches(): bool
    {
        $result = DB::selectOne("
            SELECT COUNT(ib.investment_bunch_id) as count
            FROM investment_bunch ib
            WHERE
                ib.invest_strategy_id = " . $this->invest_strategy_id . "
                AND ib.active = '1'
                AND ib.deleted = '0'
                AND ib.finished = '0'
        ");

        return (bool) $result->count > 0;
    }

    public static function getInvestorNextPriority(int $investorId): int
    {
        $priority = (int)InvestStrategy::where([
            ['investor_id', '=', $investorId],
            ['deleted', '=', 0],
        ])->max('priority');

        if ($priority > 0) {
            return ($priority + 1);
        }

        return 1;
    }

    /**
     * @return mixed
     */
    public function getOutstandingInvestment(): ?float
    {
        $res = DB::selectOne(DB::raw("
            select sum(ii.principal)
            from investor_installment ii
            join investment i on ii.investment_id = i.investment_id
            join investment_bunch ib on ib.investment_bunch_id = i.investment_bunch_id
            where
                ib.invest_strategy_id = " . $this->invest_strategy_id . "
                and ii.investor_id = " . $this->investor_id . "
                and ii.paid = 0
        "));

        return $res->sum ?? 0;
    }
}
