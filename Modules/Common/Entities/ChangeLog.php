<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class ChangeLog extends BaseModel
{
    const UPDATED_AT = null;

    protected $table = 'change_log';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'investor_id',
        'key',
        'old_value',
        'new_value',
        'user_type',
        'created_by',
        'created_by_type',
    ];

    public function getCreatorNames()
    {
        if ($this->created_by_type === 'administrator') {
            return $this->creator->two_names;
        }

        if ($this->created_by_type === 'investor') {
            $investor = $this->creatorInvestor;
            return $investor->first_name . ' ' . $investor->last_name;
        }
    }

    public function creatorInvestor()
    {
        return $this->belongsTo(
            Investor::class,
            'created_by',
            'investor_id'
        );
    }
}
