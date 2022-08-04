<?php

namespace Modules\Admin\Repositories;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Admin\Entities\Setting;
use Modules\Core\Repositories\BaseRepository;

class SettingRepository extends BaseRepository
{
    /**
     * @param int $limit
     * @param array $joins
     * @param array $where
     * @param array $order
     * @param bool $showDeleted
     *
     * @return LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $joins = [],
        array $where = [],
        array $order = [],
        bool $showDeleted = false
    ): LengthAwarePaginator {
        $where = $this->checkForDeleted($where, $showDeleted);

        $builder = Setting::orderByRaw(
            implode(', ', $this->prepareOrderStatement($order))
        );
        $this->setJoins($joins, $builder);

        if (!empty($where)) {
            $builder->where($where);
        }

        return $builder->paginate($limit);
    }

    /**
     * @param array $data
     */
    public function create(array $data)
    {
        $setting = new Setting();
        $setting->fill($data);
        $setting->save();
    }

    /**
     * @param string $settingKey
     *
     * @return mixed
     */
    public function getByKey(string $settingKey)
    {
        return Setting::find($settingKey);
    }

    /**
     * @param Setting $setting
     * @throws Exception
     */
    public function delete(Setting $setting)
    {
        $setting->delete();
    }

    /**
     * @param Setting $setting
     */
    public function disable(Setting $setting)
    {
        $setting->disable();
    }

    /**
     * @param Setting $setting
     * @param array $data
     */
    public function update(Setting $setting, array $data)
    {
        $setting->fill($data);
        $setting->save();
    }

    /**
     * @param Setting $setting
     */
    public function enable(Setting $setting)
    {
        $setting->enable();
    }

    /**
     * @param string $settingKey
     *
     * @return Setting|null
     */
    public function getSettingByKey(string $settingKey): ?Setting
    {
        return Setting::where(
            [
                'setting_key' => $settingKey,
            ]
        )->first();
    }
}
