<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class FileType extends BaseModel
{
    public const ID_CARD_ID = 1;
    public const ID_CARD_NAME = 'id_card';
    public const PASSPORT_ID = 2;
    public const PASSPORT_NAME = 'passport';
    public const NEW_LOANS_ID = 3;
    public const NEW_LOANS_NAME = 'new_loans';
    public const UNLISTED_LOANS_ID = 4;
    public const UNLISTED_LOANS_NAME = 'unlisted_loans';
    public const INVESTOR_CONTRACT_ID = 5;
    public const INVESTOR_CONTRACT_NAME = 'contract';
    public const SELFIE_ID = 6;
    public const SELFIE_NAME = 'selfie';
    public const IMAGE_BLOG_ID = 7;
    public const IMAGE_BLOG_NAME = 'blog_image';
    public const IMPORTED_PAYMENT_ID = 8;
    public const IMPORTED_PAYMENT_NAME = 'imported_payment';
    public const COMPANY_ID = 9;
    public const COMPANY_NAME = 'company_documents';

    /**
     * @var string
     */
    protected $table = 'file_type';

    /**
     * @var string
     */
    protected $primaryKey = 'file_type_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
