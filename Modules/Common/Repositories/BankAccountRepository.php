<?php

namespace Modules\Common\Repositories;

use Illuminate\Database\Eloquent\Model;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Investor;
use Modules\Core\Repositories\BaseRepository;

class BankAccountRepository extends BaseRepository
{
    /**
     * @return mixed
     */
    public function getAll()
    {
        return BankAccount::where(
            'active',
            '=',
            '1'
        )->get();
    }

    /**
     * @param int $InvestorId
     *
     * @return mixed
     */
    public function getByInvestorId(int $InvestorId)
    {
        return BankAccount::where(
            [
                ['active', '=', '1'],
                ['investor_id', '=', $InvestorId]
            ]
        )->get();
    }

    /**
     * @param int $investorId
     * @param int $bankAccountId
     *
     * @return mixed
     */
    public function update(int $investorId, int $bankAccountId)
    {

       $mainBankAccount = BankAccount::where([
            [
                'investor_id',
                '=',
                $investorId
            ],
            [
                'bank_account_id',
                '=',
                $bankAccountId
            ]
        ])->update(['default' => 1]);

        BankAccount::where([
            [
                'investor_id',
                '=',
                $investorId
            ],
            [
                'bank_account_id',
                '!=',
                $bankAccountId
            ]
        ])->update(['default' => 0]);

        return $mainBankAccount;
    }

    /**
     * @param $iban
     * @param Investor $investor
     * @param bool $default
     * @param null $bic
     *
     * @return BankAccount
     */
    public function create(
        $iban,
        Investor $investor,
        bool $default,
        $bic = null
    ): BankAccount {
        $bankAccount = new BankAccount();
        $bankAccount->fill(
            [
                'investor_id' => $investor->investor_id,
                'iban' => $iban,
                'default' => $default,
                'bic' => $bic,
            ]
        );
        $bankAccount->save();

        return $bankAccount;
    }

    /**
     * @param BankAccount $bankAccount
     * @param $bic
     *
     * @return BankAccount
     */
    public function updateBic(
        BankAccount $bankAccount,
        $bic
    ) {
        $bankAccount->bic = $bic;
        $bankAccount->save();

        return $bankAccount;
    }
}
