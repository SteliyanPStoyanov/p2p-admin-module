<?php

namespace Modules\Admin\Entities;

use Modules\Common\Entities\File;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;
use Modules\Core\Services\StorageService;

class BlogPage extends BaseModel implements LoggerInterface
{
    public const LIMIT_BLOG_PAGES = 10;

    protected $table = 'blog_page';
    protected $primaryKey = 'blog_page_id';

    protected $fillable = [
        'blog_page_id',
        'administrator_id',
        'title',
        'date',
        'tags',
        'content',
        'active',
        'file_id',
    ];

    protected $with = ['creator', 'updater'];

    public function administrator()
    {
        return $this->belongsTo(
            Administrator::class,
            'administrator_id',
            'administrator_id'
        );
    }

    public function files()
    {
        return $this->belongsToMany(
            File::class,
            'blog_file',
            'blog_page_id',
            'file_id',
        )->withPivot('file_id');
    }
}
