<?php

namespace Modules\Communication\Entities;

use Modules\Core\Models\BaseModel;

class EmailSource extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'email_source';

    /**
     * @var string
     */
    protected $primaryKey = 'email_source_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'source',
        'details'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailCampaigns()
    {
        return $this->hasMany(EmailCampaign::class);
    }
}
