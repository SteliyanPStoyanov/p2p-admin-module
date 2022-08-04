<?php

namespace Modules\Common\Entities;

use Modules\Admin\Entities\BlogPage;
use Modules\Communication\Entities\Email;
use Modules\Core\Models\BaseModel;

class File extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'file';

    protected $primaryKey = 'file_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'hash',
        'file_storage_id',
        'file_type_id',
        'file_path',
        'file_size',
        'file_type',
        'file_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileStorage()
    {
        return $this->belongsTo(FileStorage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fileType()
    {
        return $this->belongsTo(
            FileType::class,
            'file_type_id',
            'file_type_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emails()
    {
        return $this->belongsToMany(
            Email::class,
            'email_files',
            'email_id',
            'file_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(
            Document::class,
            'file_id',
            'file_id',
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blogPages()
    {
        return $this->belongsToMany(
            BlogPage::class,
            'blog_file',
            'blog_page_id',
            'file_id',
        );
    }
}
