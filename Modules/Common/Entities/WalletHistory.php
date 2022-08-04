<?php

namespace Modules\Common\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Core\Models\BaseModel;

class WalletHistory extends BaseModel
{
    public const INVESTOR_WALLET_ID = 1;

    /**
     * @var string
     */
    protected $table = 'wallet_history';

    /**
     * @var string
     */
    protected $primaryKey = 'wallet_history_id';

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(
            Wallet::class,
            'wallet_id',
            'wallet_id'
        );
    }

    /**
     * @param int $currencyId
     * @param int $investorId
     * @param string $startDate
     * @param string|null $endDate
     *
     * @return array
     */
    public function getWalletForDates(
        int $currencyId = Currency::ID_EUR,
        int $investorId,
        ?string $startDate,
        ?string $endDate
    ) {
        $result = [];
        if ($startDate) {
            $start = $this::selectRaw(
                'date, investor_id, uninvested '
            )->where(
                [
                    'currency_id' => $currencyId,
                    'investor_id' => $investorId,
                ]
            )->whereRaw(
                DB::raw(
                    "(date between '" . $startDate . "' and '" . $startDate . "')"
                )
            )
                ->orderBy('date', 'asc')
                ->first();
        }
        if ($endDate) {
            $end = $this::selectRaw(
                'date, investor_id, uninvested '
            )->where(
                [
                    'currency_id' => $currencyId,
                    'investor_id' => $investorId,
                ]
            )->whereRaw(
                DB::raw(
                    "(date between '" . $endDate . "' and '" . $endDate . "')"
                )
            )
                ->orderBy('date', 'asc')
                ->first();
        }

        isset($start) ? $result['start'] = $start->toArray() : null;
        isset($end) ? $result['end'] = $end->toArray() : null;

        return $result;
    }
}
