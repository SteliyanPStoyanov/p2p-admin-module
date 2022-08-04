<?php

namespace Modules\Admin\Entities;

use Modules\Common\Entities\ChangeLog;
use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Verification extends BaseModel implements HistoryInterface, LoggerInterface
{
    const MARK_VERIFIED = 'mark_verified';
    const REJECT_VERIFICATION = 'reject_verification';
    const REQUEST_DOCUMENTS = 'request_documents';

    protected $table = 'verification';
    protected $primaryKey = 'verification_id';

    protected $historyClass = ChangeLog::class;

    protected $fillable = [
        'investor_id',
        'comment',
        'name',
        'birth_date',
        'address',
        'citizenship',
        'photo',
    ];
}
