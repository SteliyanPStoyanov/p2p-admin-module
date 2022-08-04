<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class FileStorage extends BaseModel
{
    public const FILE_STORAGE_HARD_DISC_ONE_ID = 1;
    public const FILE_STORAGE_HARD_DISC_ONE_NAME = 'storage_hard_disc_one';

    protected $table = 'file_storage';

    protected $primaryKey = 'file_storage_id';


    protected $fillable = [
        'name',
        'disk_total',
        'disk_usage',
        'disk_space',
        'last_file_update_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

}
