<?php

namespace Modules\Common\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Loan;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class RepaidInstallment extends BaseModel implements LoggerInterface
{
    /**
     * @var string
     */
    protected $table = 'repaid_installment';

    /**
     * @var string
     */
    protected $primaryKey = 'repaid_installment_id';


    /**
     * @var string[]
     */
    protected $guarded = [
        'repaid_installment_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function loan(): ?Loan
    {
        return Loan::where('lender_id', $this->lender_id)->first();
    }

    public function installment(): ?Installment
    {
        return Installment::where(
            'lender_installment_id',
            $this->lender_installment_id
        )->first();
    }

    public function investorInstallments(): ?array
    {
        $result = DB::table('investor_installment')
            ->select('investor_installment.*')
            ->join('installment', 'installment.installment_id', '=', 'investor_installment.installment_id')
            ->join('repaid_installment', 'repaid_installment.lender_installment_id', '=', 'installment.lender_installment_id')
            ->where([
                ['repaid_installment.repaid_installment_id', '=', $this->repaid_installment_id],
                ['investor_installment.paid', '=', '0'],
            ])
            ->get();

        if (!$result->count()) {
            return null;
        }

        return InvestorInstallment::hydrate($result->all())->all();
    }

    public function handle()
    {
        $this->handled = 1;
        $this->save();
    }
}
