<?php

namespace Modules\Common\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Loan;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class RepaidLoan extends BaseModel implements LoggerInterface
{
    const TYPE_NORMAL = 'normal';
    const TYPE_EARLY = 'early';
    const TYPE_LATE = 'late';

    /**
     * @var string
     */
    protected $table = 'repaid_loan';

    /**
     * @var string
     */
    protected $primaryKey = 'repaid_loan_id';


    /**
     * @var string[]
     */
    protected $guarded = [
        'repaid_loan_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function getTypes(): array
    {
        return [
            self::TYPE_NORMAL,
            self::TYPE_EARLY,
            self::TYPE_LATE,
        ];
    }

    public function loan(): ?Loan
    {
        return Loan::where('lender_id', $this->lender_id)->first();
    }

    public function handle()
    {
        $this->handled = 1;
        $this->save();
    }
}
