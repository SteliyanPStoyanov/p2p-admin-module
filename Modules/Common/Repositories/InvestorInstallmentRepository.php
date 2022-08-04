<?php


namespace Modules\Common\Repositories;


use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Core\Repositories\BaseRepository;

class InvestorInstallmentRepository extends BaseRepository
{

    public function getInvestorInstallmentsByLoanId(int $loanId)
    {
        return InvestorInstallment::where(
            'loan_id',
            '=',
            $loanId
        )->get();
    }

    public function getInvestorInstallmentsByInstallmentId(int $installmentId)
    {
        return InvestorInstallment::where(
            'installment_id',
            '=',
            $installmentId
        )->get();
    }

    public function investorShare(int $investorId, int $loanId)
    {
        $builder = DB::table('investor_installment');
        $builder->select(
            DB::raw(
                '
           sum(principal) as share
            '
            )
        );
        $builder->whereRaw(
            'investor_id =' . $investorId . ' AND loan_id =' . $loanId . ' AND paid = 0'
        );

        return $builder->first();
    }

    /**
     * @param int|null $investorId
     * @param int $loanId
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function investorsShare(?int $investorId, int $loanId)
    {
        $builder = DB::table('investor_installment');
        $builder->select(
            DB::raw(
                '
           sum(principal) as share,
           count(distinct investor_id)
        '
            )
        );

        $sql = 'loan_id =' . $loanId . ' AND paid = 0';
        if ($investorId != null) {
            $sql = 'investor_id !=' . $investorId . ' AND loan_id =' . $loanId . ' AND paid = 0';
        }

        $builder->whereRaw($sql);

        return $builder->first();
    }

    public function getInstallmentsOutstandingAmount(int $investorId)
    {
        $results = DB::select(DB::raw("
            select
                sum(ii.principal) as principal
            from investor_installment as ii
            where
                ii.investor_id = '$investorId'
                and ii.paid = 0
        "));

        if (isset($results[0]->principal)) {
            return $results[0]->principal;
        }

        return null;
    }
}
