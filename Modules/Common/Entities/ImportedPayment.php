<?php

namespace Modules\Common\Entities;

use Modules\Common\Observers\ImportedPaymentObserver;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class ImportedPayment extends BaseModel implements LoggerInterface
{
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';

    public const STATUS_NEW = 'new';
    public const STATUS_HANDLED = 'handled';
    public const STATUS_WARNING = 'warning';

    public const DATE_REGEX = '/[0-9]{2}.[0-9]{2}.[0-9]{4}/';
    public const REASON_REGEX = '/iban/i';
    public const CURRENCY_REGEX = '/eur/i';
    public const TYPE_REGEX = '/кт/iu';
    public const AMOUNT_REGEX = '/(\d+ )?(\d+ )?(\d+ )?\d+(.\d+)?/';


    public const NAME_REGEX = '/([a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16} )(.*)( )(\-|\–)/iu';
    public const INVESTOR_ID_REGEX = '/(((No[\.]?[\s]{0,2}|N[\.]?[\s]{0,2}|#[\.]?[\s]{0,2}|Investor[\–\-\:\=\.]?[\s]{0,2}|[\–\-\:\=\.][\s]{0,2}|\s(?!(BIC|IBAN|INVESTOR)))([1-9][0-9]{5,6})($|\s|[\,\.\-\–\;\|\&]))|\(([1-9][0-9]{5,6})\))/i';
    public const IBAN_REGEX = '/((IBAN|IBAN[\s]{0,2}|IBAN[\–\-\:\=\.]?[\s]{0,2}|[\–\-\:\=\.][\s]{0,2}|[\s]{1,2}(?!(BIC|IBAN|INVESTOR)))([a-z]{2}[0-9]{2}[0-9a-z]{10,32})($|\s|[\,\.\-\–\;\|\&]))/i';
    public const BIC_REGEX = '/((BIC|BIC[\s]{0,2}|BIC[\s]{0,2}[\–\-\:\=\.][\s][\s]{0,2}|[\–\-\:\=\.][\s]{0,2}(?!(BIC|IBAN|INVESTOR))|\s{1,}(?!(BIC|IBAN|INVESTOR)))([a-z]{6}[0-9a-z]{2}([0-9a-z]{0,3})?)($|\s|[\,\.\-\–\;\|\&]))/i';

    public const PAYMENT_STARTER_REGEX = '/((SEPA получен кредитен превод)([\s]?\-[\s]?)?)/';

    protected $table = 'imported_payment';

    protected $primaryKey = 'imported_payment_id';

    protected $guarded = [
        'imported_payment_id',
        'active',
        'deleted',
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

    public static function boot()
    {
        parent::boot();

        self::observe(ImportedPaymentObserver::class);
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_IN,
            self::TYPE_OUT,
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_HANDLED,
            self::STATUS_WARNING,
        ];
    }

    public static function existsById($bankTransactionId)
    {
        return (self::where('bank_transaction_id', $bankTransactionId)->count() > 0);
    }
}
