<?php

namespace Modules\Admin\Entities;

use App;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\ChangeLog;
use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseAuthModel;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\StorageService;
use Modules\Core\Traits\CustomPermissionTrait;

class Administrator extends BaseAuthModel implements HistoryInterface, LoggerInterface
{
    use CustomPermissionTrait;

    private const MIN_PRIORITY_FOR_CHANGING_ROLES = 80; // unit test user
    protected const CACHE_TIME_PERMISSIONS = 600;

    public const SYSTEM_ADMINISTRATOR_ID = 1; // quiet daemon
    public const DEFAULT_ADMINISTRATOR_ID = 2; // super admin
    public const DEFAULT_UNIT_TEST_USER_ID = 3; // unit test user
    public const ADMINISTRATOR_STATISTIC_DAYS = [7, 14, 30, '2021-01-01'];

    protected $guard = 'web';

    protected $historyClass = ChangeLog::class;

    /**
     * @var string
     */
    protected $table = 'administrator';

    /**
     * @var string
     */
    protected $primaryKey = 'administrator_id';

    /**
     * @var string[]
     */
    protected $with = ['roles', 'permissions', 'creator', 'updater'];

    /**
     * @var string[]
     */
    protected $appends = ['twoNames'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'email',
        'avatar',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'remember_token',
        'password'
    ];

    /**
     * Returns first and last names
     *
     * @return string
     */
    public function getTwoNamesAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns first, middle and last names
     *
     * @return string
     */
    public function getFullNames(): string
    {
        return $this->first_name
            . ' ' . $this->middle_name
            . ' ' . $this->last_name;
    }

    /**
     * [getAvatarPath description]
     *
     * @return string
     */
    public function getAvatarPath(): string
    {
        $path = StorageService::getAdminAvatarPath($this->getId());
        if (!StorageService::hasFile($path)) {
            return StorageService::DEFAULT_AVATAR_PATH;
        }
        return StorageService::DEFAULT_STORAGE_PATH . $path;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function settings()
    {
        return $this->belongsToMany(
            Setting::class,
            'administrator_setting',
            'administrator_id',
            'setting_key'
        )->withPivot('value');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mail()
    {
        return $this->hasMany(Mail::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sms()
    {
        return $this->hasMany(Sms::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function email()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * [role description]
     *
     * @return Role
     */
    public function role()
    {
        return DB::table('role')
            ->join(
                'administrator_role',
                'administrator_role.role_id',
                '=',
                'role.id'
            )->where(
                [
                    'administrator_id' => $this->administrator_id,
                ]
            )->first();
    }

    /**
     * [getPriority description]
     *
     * @return int
     */
    public function getPriority(): int
    {
        $role = $this->role();
        if (empty($role->priority)) {
            return 0;
        }

        return (int)$role->priority;
    }

    /**
     * [canChangeRoles description]
     *
     * @return int
     */
    public function canChangeRoles()
    {
        return (self::MIN_PRIORITY_FOR_CHANGING_ROLES <= $this->getPriority());
    }

    /**
     * @param array $permissionsGroups
     *
     * @return array|mixed
     */
    public function resolvePermissions(array $permissionsGroups)
    {
        $cachedAllowedPermissionsKey = 'allowed_permissions_' . $this->administrator_id;
        $cacheService = App::make(CacheService::class);

        $allowed = $cacheService->get($cachedAllowedPermissionsKey, true);
        if (!$allowed) {
            $allowed = [];

            foreach ($permissionsGroups as $section => $permissions) {
                foreach ($permissions as $permission) {
                    $permissionName = 'admin.' . $permission . '.list';

                    $allowed[$section][$permission] = $this->hasPermissionTo($permissionName);
                }

                if (!in_array(true, $allowed[$section])) {
                    $allowed[$section] = false;
                }
            }

            $cacheService->set(
                $cachedAllowedPermissionsKey,
                $allowed,
                self::CACHE_TIME_PERMISSIONS
            );
        }

        return $allowed;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogPages()
    {
        return $this->hasMany(BlogPage::class);
    }
}
