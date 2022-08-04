<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class LoanAmountAvailable extends BaseModel implements LoggerInterface
{
    public const TYPE_INVESTMENT = 'investment';
    public const TYPE_REPAYMENT = 'repayment';

    protected $table = 'loan_amount_available';
    protected $primaryKey = 'loan_amount_available_id';
    protected $fillable = [
        'loan_id',
        'amount_before',
        'amount_after',
        'type',
        'investment_id',
        'installment_id'
    ];

    public static function getTypes(): array
    {
        return [
            self::TYPE_INVESTMENT,
            self::TYPE_REPAYMENT
        ];
    }

    /**
     * @return BelongsTo
     */
    public function loan():BelongsTo
    {
        return $this->belongsTo(
            Loan::class,
            'loan_id',
            'loan_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(
            Investment::class,
            'investment_id',
            'investment_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(
            Installment::class,
            'installment_id',
            'installment_id'
        );
    }
}
