<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class DocumentDownloadLog extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'document_download_log';

    /**
     * @var string
     */
    protected $primaryKey = 'document_download_log_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'document_download_log_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(
            Document::class,
            'document_id',
            'document_id'
        );
    }
}
