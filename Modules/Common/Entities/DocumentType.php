<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class DocumentType extends BaseModel
{

    public const DOCUMENT_TYPE_ID_IDCARD = 1;
    public const DOCUMENT_TYPE_ID_PASSPORT = 2;
    public const DOCUMENT_TYPE_FROM_ADMIN_ID = 3;
    public const DOCUMENT_TYPE_SELFIE_ID = 4;
    public const DOCUMENT_TYPE_ID_COMPANY = 5;
    public const DOCUMENT_TYPE_NAME_IDCARD = 'ID Card';
    public const DOCUMENT_TYPE_NAME_PASSPORT = 'Passport';
    public const DOCUMENT_TYPE_FROM_ADMIN = 'Uploaded file from admin';
    public const DOCUMENT_TYPE_SELFIE = 'Selfie';
    public const DOCUMENT_TYPE_NAME_COMPANY = 'Company documents';

    /**
     * @var string
     */
    protected $table = 'document_type';

    /**
     * @var string
     */
    protected $primaryKey = 'document_type_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'active',
        'deleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'enabled_at',
        'enabled_by',
        'disabled_at',
        'disabled_by',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function document()
    {
        return $this->hasMany(
            Document::class,
            'document_type_id',
            'document_type_id'
        );
    }
}
