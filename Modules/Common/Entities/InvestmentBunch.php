<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;
use Modules\Common\Services\LoanService;

class InvestmentBunch extends BaseModel implements LoggerInterface
{
    private $loanService = null;
    private $brother = null;

    /**
     * @var string
     */
    protected $table = 'investment_bunch';

    /**
     * @var string
     */
    protected $primaryKey = 'investment_bunch_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'investment_bunch_id',
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

    public function isFinished(): bool
    {
        return (1 == $this->finished);
    }

    public function isFull(): bool
    {
        return ($this->count >= $this->total);
    }

    public function isMultiRun(): bool
    {
        return (1 == $this->multi_run);
    }

    /**
     * @return HasMany
     */
    public function investments(): HasMany
    {
        return $this->hasMany(
            Investment::class,
            'investment_bunch_id',
            'investment_bunch_id'
        )->orderBy('loan_id', 'desc');
    }

    /**
     * @return BelongsTo
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return Model|BelongsTo|object|null
     */
    public function investStrategy()
    {
        return $this->belongsTo(
            InvestStrategy::class,
            'invest_strategy_id',
            'invest_strategy_id'
        )->first();
    }

    public function cartSecondary()
    {
        return $this->hasOne(
            CartSecondary::class,
            'cart_secondary_id',
            'cart_secondary_id'
        );
    }

    public function getSellerThroughBuyersCart()
    {
        $sellersIds = [];
        foreach ($this->cartSecondary->loansForInvestment as $loan) {
            $sellersIds[] = $loan->marketSecondary->loanOnSale->cartForInvestments->investor_id;
        }

        return $sellersIds;
    }

    public function hasActiveBrother(): bool
    {
        $brother = $this->getBrother();

        if (null !== $brother && !empty($brother->investment_bunch_id)) {
            return true;
        }

        return false;
    }

    public function getBrother(): ?InvestmentBunch
    {
        if (empty($this->investment_bunch_id)) {
            return null;
        }

        return InvestmentBunch::where([
            ['active', '=', '1'],
            ['deleted', '=', '0'],
            ['finished', '=', '0'],
            ['investor_id', '=', $this->investor_id],
            ['investment_bunch_id', '!=', $this->investment_bunch_id],
        ])->first();
    }

    public function getActiveBrothersCount(): int
    {
        if (empty($this->investment_bunch_id) || empty($this->investor_id)) {
            return 0;
        }

        $result = DB::selectOne("
            SELECT COUNT(ib.investment_bunch_id) as count
            FROM investment_bunch ib
            WHERE
                ib.investor_id = " . $this->investor_id . "
                AND ib.investment_bunch_id != " . $this->investment_bunch_id . "
                AND ib.active = '1'
                AND ib.deleted = '0'
                AND ib.finished = '0'
        ");

        return (int) $result->count;
    }

    public function hasActiveMultiRunBunchesWithHigherPrioity()
    {
        return ($this->getActiveMultiRunBunchesCountWithHigherPrioity() > 0);
    }

    public function getActiveMultiRunBunchesCountWithHigherPrioity(): int
    {
        if (empty($this->investment_bunch_id) || empty($this->investor_id)) {
            return 0;
        }

        $result = DB::selectOne("
            SELECT COUNT(ib.investment_bunch_id) as count
            FROM investment_bunch ib
            JOIN invest_strategy ist ON (
                ist.invest_strategy_id = ib.invest_strategy_id
                AND ist.active = '1'
                AND ist.deleted = '0'
                AND ist.priority < " . intval($this->priority) . "
            )
            WHERE
                ib.invest_strategy_id IS NOT NULL
                AND ib.active = '1'
                AND ib.deleted = '0'
                AND ib.finished = '0'
                AND ib.multi_run = '1'
        ");

        return (int) $result->count;
    }

    private function getLoanService()
    {
        if (null === $this->loanService) {
            $this->loanService = \App::make(LoanService::class);
        }

        return $this->loanService;
    }

    public function finish(string $msg = '')
    {
        $this->details = (!empty($this->details) ? $this->details . '; ' : '') . $msg;
        $this->finished = 1;
        $this->save();
    }

    public function addDetails(string $msg)
    {
        $this->details = (string) $this->details . '; ' . $msg;
        $this->save();
    }

    public function addCount(int $count)
    {
        $this->count += $count;
        $this->save();
    }
}
