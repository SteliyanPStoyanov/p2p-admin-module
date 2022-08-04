<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Currency extends BaseModel implements LoggerInterface
{
    public const ID_EUR = 1;
    public const ID_BGN = 3;
    public const LABEL_LEV = 'bgn';
    public const LABEL_EURO = 'eur';
    public const CURRENCY_RATE = 1.95583;

    protected $table = 'currency';

    protected $primaryKey = 'currency_id';

    protected $fillable = [
        'name',
    ];
}
