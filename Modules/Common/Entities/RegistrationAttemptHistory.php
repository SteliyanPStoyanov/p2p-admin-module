<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class RegistrationAttemptHistory extends BaseModel
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'registration_attempt_history';

    /**
     * @var string
     */
    protected $primaryKey = 'history_id';
}
