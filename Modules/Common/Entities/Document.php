<?php

namespace Modules\Common\Entities;

use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Models\BaseModel;

class Document extends BaseModel implements HistoryInterface
{
    /**
     * @var string
     */
    protected $table = 'document';

    /**
     * @var string
     */
    protected $primaryKey = 'document_id';

    /**
     * @var string
     */
    protected $historyClass = ChangeLog::class;

    /**
     * @var string[]
     */
    protected $fillable = [
        'document_type_id',
        'investor_id',
        'file_id',
        'name',
        'description',
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documentType()
    {
        return $this->belongsTo(
            DocumentType::class,
            'document_type_id',
            'document_type_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(
            File::class,
            'file_id',
            'file_id'
        );
    }
}
