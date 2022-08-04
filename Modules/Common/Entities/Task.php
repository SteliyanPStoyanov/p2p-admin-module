<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Admin\Entities\Administrator;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Task extends BaseModel implements LoggerInterface
{
    public const TASK_TYPE_VERIFICATION = 'verification';
    public const TASK_TYPE_WITHDRAW = 'withdraw';
    public const TASK_TYPE_BONUS_PAYMENT = 'bonus_payment';
    public const TASK_TYPE_FIRST_DEPOSIT = 'first_deposit';
    public const TASK_TYPE_MATCH_DEPOSIT = 'match_deposit';
    public const TASK_TYPE_NOT_VERIFIED = 'not_verified';
    public const TASK_TYPE_REJECTED_VERIFICATION = 'rejected_verification';

    public const TASK_MODAL_ADD_BONUS = 'modal-add-bonus';
    public const TASK_MODAL_FIRST_DEPOSIT = 'modal-first-deposit';
    public const TASK_MODAL_MATCH_DEPOSIT = 'modal-match-deposit';
    public const TASK_MODAL_NOT_VERIFIED = 'modal-not-verified';
    public const TASK_MODAL_REJECT_VERIFICATION = 'modal-reject-verification';
    public const TASK_MODAL_WITHDRAW = 'modal-withdraw';

    public const TASK_STATUS_NEW = 'new';
    public const TASK_STATUS_PROCESSING = 'processing';
    public const TASK_STATUS_DONE = 'done';
    public const TASK_STATUS_CANCEL = 'cancel';

    /**
     * @var string
     */
    protected $table = 'task';

    /**
     * @var string
     */
    protected $primaryKey = 'task_id';


    /**
     * @var string[]
     */
    protected $guarded = [
        'task_id',
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

    public static function getTypes()
    {
        return [
            self::TASK_TYPE_WITHDRAW,
            self::TASK_TYPE_VERIFICATION,
            self::TASK_TYPE_BONUS_PAYMENT,
            self::TASK_TYPE_FIRST_DEPOSIT,
            self::TASK_TYPE_MATCH_DEPOSIT,
            self::TASK_TYPE_NOT_VERIFIED,
            self::TASK_TYPE_REJECTED_VERIFICATION,
        ];
    }

    /**
     * @param string|null $key
     * @return string|string[]
     */
    public static function getTaskModalByType(string $key = null)
    {
        $map = [
            self::TASK_TYPE_WITHDRAW => self::TASK_MODAL_WITHDRAW,
            self::TASK_TYPE_BONUS_PAYMENT => self::TASK_MODAL_ADD_BONUS,
            self::TASK_TYPE_FIRST_DEPOSIT => self::TASK_MODAL_FIRST_DEPOSIT,
            self::TASK_TYPE_MATCH_DEPOSIT => self::TASK_MODAL_MATCH_DEPOSIT,
            self::TASK_TYPE_NOT_VERIFIED => self::TASK_MODAL_NOT_VERIFIED,
            self::TASK_TYPE_REJECTED_VERIFICATION => self::TASK_MODAL_REJECT_VERIFICATION
        ];

        if (!empty($key)) {
            return $map[$key];
        }

        return $map;
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
     * @return BelongsTo
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(
            BankAccount::class,
            'bank_account_id',
            'bank_account_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(
            Wallet::class,
            'wallet_id',
            'wallet_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function processingBy(): BelongsTo
    {
        return $this->belongsTo(
            Administrator::class,
            'processing_by',
            'administrator_id'
        );
    }

    /**
     * @return HasOne
     */
    public function blockedAmountHistory(): HasOne
    {
        return $this->hasOne(
            BlockedAmountHistory::class,
            'task_id',
            'task_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function importedPayment(): BelongsTo
    {
        return $this->belongsTo(
            ImportedPayment::class,
            'imported_payment_id',
            'imported_payment_id'
        );
    }

    public function getBankAccount(): ?BankAccount
    {
        return BankAccount::where(
            [
                ['bank_account_id', '=', $this->bank_account_id],
                ['investor_id', '=', $this->investor_id],
                ['deleted', '=', '0'],
                ['active', '=', '1'],
            ]
        )->first();
    }
}
