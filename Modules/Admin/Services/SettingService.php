<?php

namespace Modules\Admin\Services;

use Modules\Admin\Entities\Setting;
use Modules\Admin\Repositories\SettingRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use \Modules\Core\Services\BaseService;
use Throwable;

class SettingService extends BaseService
{
    protected SettingRepository $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;

        parent::__construct();
    }

    /**
     * @param int $limit
     * @param array $data
     *
     * @return mixed
     */
    public function getByFilters(
        int $limit,
        array $data
    ) {
        return $this->settingRepository->getAll(
            $limit,
            $this->getJoins($data),
            $this->getWhereConditions($data),
            [
                'active' => 'DESC',
            ]
        );
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getJoins(array $data): array
    {
        return [];
    }

    /**
     * @param array $data
     *
     * @return bool
     *
     * @throws ProblemException
     */
    public function create(array $data)
    {
        $data['setting_key'] = $this->transformIntoKey($data['name']);
        try {
            $this->settingRepository->create($data);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::settingCrud.CreationFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * @param string $settingKey
     * @param array $data
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function edit(string $settingKey, array $data)
    {
        $setting = $this->getSettingById($settingKey);

        try {
            $this->settingRepository->update($setting, $data);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::settingCrud.UpdatingFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * @param string $settingKey
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable(string $settingKey)
    {
        $setting = $this->getSettingById($settingKey);

        if ($setting->isActive()) {
            throw new ProblemException(__('admin::settingCrud.Enable'));
        }

        try {
            $this->settingRepository->enable($setting);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::settingCrud.ActivationFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * @param string $settingKey
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable(string $settingKey)
    {
        $setting = $this->getSettingById($settingKey);

        if (!$setting->isActive()) {
            throw new ProblemException(__('admin::settingCrud.Disable'));
        }

        try {
            $this->settingRepository->disable($setting);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::settingCrud.DeactivationFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * @param string $settingKey
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete(string $settingKey)
    {
        $setting = $this->getSettingById($settingKey);

        try {
            $this->settingRepository->delete($setting);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::settingCrud.DeletionFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * @param string $settingKey
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function getSettingById(string $settingKey)
    {
        $setting = $this->settingRepository->getByKey($settingKey);
        if (!$setting || $setting->isDeleted()) {
            throw new NotFoundException(__('admin::settingCrud.NotFound'));
        }

        return $setting;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function transformIntoKey(string $name)
    {
        return strtolower(
            str_replace(
                ' ',
                '_',
                ($name)
            )
        );
    }

    /**
     * @param string $settingKey
     *
     * @return Setting
     */
    public function getSettingBySettingKey(string $settingKey)
    {
        return $this->settingRepository->getSettingByKey($settingKey);
    }
}
