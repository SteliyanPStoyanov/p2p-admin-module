<?php

namespace Modules\Core\Models;

class InvestorLogger extends BaseLoggerModel
{
    /**
     * @var string
     */
    protected $collection = 'investor_log_collection';

    /**
     * @var string[]
     */
    protected $fillable = [
        'table',
        'action',
        'loan_id',
        'investor_id',
        'object_prev_state',
        'object_cur_state',
        'changes',
        'administrator_id',
    ];
}
