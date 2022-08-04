<?php

namespace Modules\Communication\Entities;

use Modules\Core\Models\BaseModel;

class EmailCampaign extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'email_campaign';

    /**
     * @var string
     */
    protected $primaryKey = 'email_campaign_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'sender_email',
        'sender_name',
        'reply_email',
        'reply_name',
        'period',
        'products',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function communicationEvents()
    {
        return $this->belongsToMany(
            CommunicationEvent::class,
            'communication_event_email_campaign',
            'communication_event_id',
            'email_campaign_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function email()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailSource()
    {
        return $this->belongsTo(EmailSource::class);
    }
}
