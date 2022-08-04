<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Role;
use Modules\Core\Database\Collections\CustomEloquentCollection;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Models\PivotLogger;
use Modules\Core\Observers\ModelObserver;
use Schema;

trait BaseModelTrait
{
    /**
     * @var array
     */
    protected $traitCasts = [
        // 'active' => 'boolean',
        // 'deleted' => 'boolean',
        // 'created_at' => 'datetime:d-m-Y H:i',
        // 'updated_at' => 'datetime:d-m-Y H:i',
        // 'deleted_at' => 'datetime:d-m-Y H:i',
        // 'enabled_at' => 'datetime:d-m-Y H:i',
        // 'disabled_at' => 'datetime:d-m-Y H:i',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'enabled_by' => 'integer',
        'disabled_by' => 'integer',
    ];

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(ModelObserver::class);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function delete()
    {
        if (!Schema::hasColumn($this->getTable(), 'deleted_by')) {
            parent::delete();

            return;
        }

        $this->disable();
        $this->deleted_by = self::getInvestorId() ? self::getInvestorId() : self::getAdminId();
        $this->deleted_at = Carbon::now();
        $this->deleted = 1;
        $this->active = 0;
        $this->save();
    }

    /**
     * @return void
     */
    public function disable()
    {
        $userId = self::getInvestorId() ? self::getInvestorId() : self::getAdminId();
        $this->updated_by = $userId;
        $this->updated_at = Carbon::now();
        $this->disabled_at = Carbon::now();
        $this->disabled_by = $userId;
        $this->active = 0;
        $this->save();
    }

    /**
     * @return void
     */
    public function enable()
    {
        $userId = self::getInvestorId() ? self::getInvestorId() : self::getAdminId();
        $this->updated_by = $userId;
        $this->updated_at = Carbon::now();
        $this->enabled_at = Carbon::now();
        $this->enabled_by = $userId;
        $this->active = 1;
        $this->save();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (1 == $this->active);
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return (1 == $this->deleted);
    }

    /**
     * @param array $models
     *
     * @return CustomEloquentCollection
     */
    public function newCollection(array $models = [])
    {
        return new CustomEloquentCollection($models);
    }

    /**
     * Get the admin that create the item.
     */
    public function creator()
    {
        return $this->belongsTo(
            'Modules\Admin\Entities\Administrator',
            'created_by',
            'administrator_id'
        )->select(
            ['administrator_id', 'first_name', 'middle_name', 'last_name']
        );
    }

    /**
     * Get the last admin that updated the item.
     */
    public function updater()
    {
        return $this->belongsTo(
            'Modules\Admin\Entities\Administrator',
            'updated_by',
            'administrator_id'
        )->select(
            ['administrator_id', 'first_name', 'middle_name', 'last_name']
        );
    }

    /**
     * Get the admin that deleted the item.
     */
    public function deleter()
    {
        return $this->belongsTo(
            'Modules\Admin\Entities\Administrator',
            'deleted_by',
            'administrator_id'
        )->select(
            ['administrator_id', 'first_name', 'middle_name', 'last_name']
        );
    }

    /**
     * @return string
     */
    public function getCreateAdmin()
    {
        return $this->getAdminName('creator');
    }

    /**
     * @return string
     */
    public function getUpdateAdmin()
    {
        return $this->getAdminName('updater');
    }

    /**
     * @return string
     */
    public function getDeleteAdmin()
    {
        return $this->getAdminName('deleter');
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getAdminName(string $type = 'creator'): string
    {
        return (is_null($this->{$type}) ? '' : $this->{$type}->twoNames);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->{$this->primaryKey};
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return !empty($this->getId());
    }

    /**
     * @return int
     */
    public static function getAdminId()
    {
        $admin = \Auth::user();
        if (empty($admin->administrator_id)) {
            return Administrator::SYSTEM_ADMINISTRATOR_ID;
        }

        return $admin->administrator_id;
    }

    /**
     * @return int
     */
    public static function getInvestorId()
    {
        $investor = \Auth::guard('investor')->user();
        if (empty($investor->investor_id)) {
            return null;
        }
        return $investor->investor_id;
    }

    /**
     * @return int
     */
    public function getMaxPriority(): int
    {
        return $this->roles->max('priority') ?? Role::PRIORITY_MIN;
    }

    /**
     * @param string $relation
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|array $ids
     * @param bool $detaching
     *
     * @throws NotFoundException
     */
    public function adopt(string $relation, $ids, $detaching = true)
    {
        if (!method_exists($this, $relation)) {
            throw new NotFoundException(
                'The relation ' . $relation . ' do not exists.', 500
            );
        }

        $changes = $this->$relation()->sync($ids, $detaching);
        if (empty($changes['attached']) && empty($changes['detached']) && empty($changes['updated'])) {
            return;
        }

        $log = [
            'model_id' => $this->getId(),
            'attached' => $changes['attached'],
            'detached' => $changes['detached'],
            'updated' => $changes['updated'],
            'administrator_id' => \Auth::user()->administrator_id,
            'table' => $this->getTable(),
            'relation' => $relation
        ];

        try {
            $logger = new PivotLogger();
            $logger->fill($log);
            $logger->save();
        } catch (\Throwable $e) {
            Log::channel('mongo')->error($e->getMessage());
        }
    }
}
