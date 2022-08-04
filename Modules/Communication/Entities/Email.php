<?php

namespace Modules\Communication\Entities;

use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\File;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Core\Models\BaseModel;

class Email extends BaseModel
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'email';

    /**
     * @var string
     */
    protected $primaryKey = 'email_id';

    protected $with = ['emailTemplate', 'emailCampaign', 'administrator'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'email_template_id',
        'investor_id',
        'identifier',
        'sender_from',
        'sender_to',
        'sender_reply',
        'title',
        'body',
        'text',
        'response',
        'queue',
        'queued_at',
        'tries',
        'send_at'
    ];

    public static function getEmailTypes()
    {
        return [
            'system',
            'marketing',
            'manual'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function investors()
    {
        return $this->belongsToMany(
            Loan::class,
            'email_pivot',
            'investor_id',
            'email_id',
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(
            File::class,
            'email_pivot',
            'email_id',
            'file_id',
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailTemplate()
    {
        return $this->belongsTo(
            EmailTemplate::class,
            'email_template_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailCampaign()
    {
        return $this->belongsTo(
            EmailCampaign::class,
            'email_campaign_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function administrator()
    {
        return $this->belongsTo(
            Administrator::class,
            'administrator_id'
        );
    }
}
