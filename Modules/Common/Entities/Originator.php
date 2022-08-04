<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Originator extends BaseModel implements LoggerInterface
{
    public const ID_ORIG_STIKCREDIT = '1';
    public const NAME_ORIG_STIKCREDIT = 'Stikcredit';
    public const PHONE_ORIG_STIKCREDIT = '070010514';
    public const EMAIL_ORIG_STIKCREDIT = 'info@stikcredit.bg';
    public const WEBSITE_ORIG_STIKCREDIT = 'www.stikcredit.bg';
    public const IBAN_ORIG_STIKCREDIT = 'BG68FINV91501016892097';
    public const PIN_STIKCREDIT = '202557159';

    protected $table = 'originator';

    protected $primaryKey = 'originator_id';

    protected $with = ['country'];

    protected $fillable = [
        'name',
        'description',
        'phone',
        'email',
        'website',
        'iban',
    ];

    public function country()
    {
        return $this->belongsTo(
            Country::class,
            'country_id',
            'country_id'
        );
    }
}
