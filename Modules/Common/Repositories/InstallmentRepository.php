<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Core\Repositories\BaseRepository;

class InstallmentRepository extends BaseRepository
{
    /**
     * @param $installmentId
     * @param array $data
     *
     * @return bool
     */
    public function update($installmentId, array $data)
    {
        return Installment::where('installment_id', $installmentId)->update($data);
    }

    public function updateInvestorInstallmentAccruedInterest(
        InvestorInstallment $investorInstallment,
        float $accruedInterest
    ) {
        $investorInstallment->accrued_interest = $accruedInterest;
        $investorInstallment->save();

        return $investorInstallment;
    }

    public function updateInvestorInstallmentLateInterest(
        InvestorInstallment $investorInstallment,
        float $lateInterest
    ) {
        $investorInstallment->late_interest = $lateInterest;
        $investorInstallment->accrued_interest = $investorInstallment->interest;
        $investorInstallment->save();

        return $investorInstallment;
    }

    public function getInstallmentsByLoanId(int $loanId)
    {
        return Installment::where(
            'loan_id',
            '=',
            $loanId
        )->get();
    }
}
