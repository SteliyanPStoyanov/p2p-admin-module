<?php

namespace Modules\Common\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Core\Repositories\BaseRepository;

class WalletRepository extends BaseRepository
{
    /**
     * @param Transaction $transaction
     *
     * @return Wallet $wallet
     */
    public function withdraw(Transaction $transaction)
    {
        $wallet = $transaction->wallet;
        $wallet->withdraw = $wallet->withdraw + $transaction->amount;
        $wallet->blocked_amount -= $transaction->amount;

        $wallet->save();

        return $wallet;
    }

    /**
     * @param $wallet
     * @param $transaction
     */
    public function withBonus(Transaction $transaction)
    {
        $wallet = $transaction->wallet;
        $wallet->bonus = $wallet->bonus + $transaction->amount;
        $wallet->income = $wallet->income + $transaction->amount;
        $wallet->uninvested = $wallet->uninvested + $transaction->amount;

        $wallet->save();

        return $wallet;
    }

    /**
     * @param int $investorId
     * @param array $data
     *
     * @return mixed
     */
    public function walletUpdate(int $investorId, array $data)
    {
        return Wallet::where('investor_id', '=', $investorId)->update($data);
    }

    /**
     * @param int $investorId
     *
     * @return mixed
     */
    public function getByInvestorId(int $investorId)
    {
        return Wallet::where([
            ['investor_id', '=', $investorId],
            ['active', '=', '1'],
            ['deleted', '=', '0'],
        ])->first();
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['active' => 'DESC', 'wallet_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $where = $this->checkForDeleted($where, $showDeleted, 'wallet');

        $builder = DB::table('wallet');
        $builder->select(
            DB::raw(
                '
            wallet.*
            '
            )
        );
        $builder->join(
            'investor',
            'wallet.investor_id',
            '=',
            'investor.investor_id',
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = Wallet::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return Wallet
     */
    public function createWallet(array $data)
    {
        $wallet = new Wallet();
        $wallet->fill($data);
        $wallet->save();

        return $wallet;
    }

    /**
     * @param $wallet
     * @param $amount
     *
     * @return bool
     */
    public function returnBlockedAmount($wallet, $amount): bool
    {
        $blocked = $wallet->blocked_amount;
        if (($blocked - $amount) < 0) {
            return false;
        }


        $wallet->uninvested += $amount;
        $wallet->blocked_amount -= $amount;
        $wallet->save();
        return true;
    }
}
