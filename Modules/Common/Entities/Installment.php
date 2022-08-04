<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Installment extends BaseModel implements LoggerInterface
{
    public const STATUS_PAID = 'paid';
    public const STATUS_PAID_ID = 1;
    public const STATUS_PAID_LATE = 'paid late';
    public const STATUS_LATE = 'late';
    public const STATUS_SCHEDULED = 'scheduled';

    /**
     * @var string
     */
    protected $table = 'installment';

    /**
     * @var string
     */
    protected $primaryKey = 'installment_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'installment_id',
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
     * @return array
     */
    public static function getPaymentStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_PAID,
            self::STATUS_PAID_LATE,
            self::STATUS_LATE,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(
            Loan::class,
            'loan_id',
            'loan_id'
        );
    }

    public function pay(Carbon $repaymentDate = null)
    {
        if (null === $repaymentDate) {
            $repaymentDate = Carbon::now();
        }

        $date1 = clone $repaymentDate;
        $date1->setTime(0, 0, 0);
        $date2 = Carbon::parse($this->due_date);
        $date2->setTime(0, 0, 0);

        $this->paid = 1;
        $this->paid_at = $repaymentDate;
        $this->payment_status = (
            $date1->lte($date2)
            ? self::STATUS_PAID
            : self::STATUS_PAID_LATE
        );

        $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function investorInstallments()
    {
        return $this->hasMany(
            InvestorInstallment::class,
            'installment_id',
            'installment_id'
        );
    }

    public function getPreviousInstallmentDueDate(bool $returnNullIfNoPrevInstallment = false)
    {
        $installment = $this->getPreviousInstallment();

        if (null === $installment) {
            if ($returnNullIfNoPrevInstallment) {
                return null;
            }

            return Carbon::parse($this->loan->created_at)->format('Y-m-d');
        }

        return $installment->due_date;
    }

    public function getPreviousInstallment()
    {
        if ($this->seq_num == 1) {
            return null;
        }

        return Installment::where([
            ['loan_id', '=', $this->loan_id],
            ['seq_num', '=', ($this->seq_num - 1)],
        ])->first();
    }

    public function getNextInstallment()
    {
        return Installment::where([
            'loan_id' => $this->loan_id,
            'seq_num' => ($this->seq_num + 1),
        ])->first();
    }

    public function isLast(): bool
    {
        $count = Installment::where([
            ['loan_id', '=', $this->loan_id],
            ['paid', '=', 0],
        ])->count();
        return ($count < 1 ? true : false);
    }

}
