<?php

namespace Modules\Admin\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Repositories\AdministratorRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\StorageService;
use \Modules\Core\Services\BaseService;
use Throwable;

class AdministratorService extends BaseService
{
    private AdministratorRepository $administratorRepository;
    private StorageService $storageService;
    private RoleService $roleService;

    /**
     * AdministratorService constructor.
     *
     * @param AdministratorRepository $administratorRepository
     * @param StorageService $storageService
     * @param RoleService $roleService
     */
    public function __construct(
        AdministratorRepository $administratorRepository,
        StorageService $storageService,
        RoleService $roleService
    ) {
        $this->administratorRepository = $administratorRepository;
        $this->storageService = $storageService;
        $this->roleService = $roleService;

        parent::__construct();
    }

    /**
     * @param array $data
     * @param Administrator $administrator
     *
     * @return Administrator
     *
     * @throws ProblemException
     */
    public function create(array $data, Administrator $administrator)
    {
        if (!empty($data['roles'])) {
            $this->roleService->canManageRoles(
                $administrator,
                $data['roles']
            );
        }

        $data['password'] = Hash::make($data['password']);

        try {
            $admin = $this->administratorRepository->create($data);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::adminCrud.adminCreationFailed')
            );
        }

        return $admin;
    }

    /**
     * @param array $data
     * @param Administrator $loggedAdministrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function update(
        array $data,
        Administrator $loggedAdministrator
    ) {
        if (!empty($data['roles'])) {
            $this->roleService->canManageRoles(
                $loggedAdministrator,
                $data['roles']
            );
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $administrator = $this->getAdministratorById($data['administrator_id']);

        try {
            $this->administratorRepository->update($administrator, $data);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::adminCrud.adminEditionFailed')
            );
        }

        (new CacheService())->remove('allowed_permissions_' . $administrator->administrator_id);

        return true;
    }

    /**
     * @param int $administratorId
     * @param Administrator $loggedAdministrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete(
        int $administratorId,
        Administrator $loggedAdministrator
    ) {
        $administrator = $this->getAdministratorById($administratorId);
        $this->canControl($loggedAdministrator, $administrator);

        try {
            $this->administratorRepository->delete($administrator);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::adminCrud.adminDeletionFailed')
            );
        }

        return true;
    }

    /**
     * @param int $administratorId
     * @param Administrator $loggedAdministrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable(
        int $administratorId,
        Administrator $loggedAdministrator
    ) {
        $administrator = $this->getAdministratorById($administratorId);
        $this->canControl($loggedAdministrator, $administrator);
        if ($administrator->isActive()) {
            throw new ProblemException(
                __('admin::adminCrud.adminEnableForbidden')
            );
        }

        try {
            $this->administratorRepository->enable($administrator);
        } catch (Throwable $exception) {
            throw new ProblemException(
                __('admin::adminCrud.adminEnableFailed')
            );
        }

        return true;
    }

    /**
     * @param int $administratorId
     * @param Administrator $loggedAdministrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable(
        int $administratorId,
        Administrator $loggedAdministrator
    ) {
        $administrator = $this->getAdministratorById($administratorId);
        $this->canControl($loggedAdministrator, $administrator);
        if (!$administrator->isActive()) {
            throw new ProblemException(
                __('admin::adminCrud.adminEnableForbidden')
            );
        }

        try {
            $this->administratorRepository->disable($administrator);
        } catch (\Throwable $exception) {
            throw new ProblemException(
                __('admin::adminCrud.adminDisableFailed')
            );
        }

        return true;
    }

    /**
     * @param int $limit
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(int $limit, array $data)
    {
        $whereConditions = $this->getWhereConditions(
            $data,
            [
                'administrator.first_name',
                'administrator.middle_name',
                'administrator.last_name'
            ],
            'administrator'
        );

        return $this->administratorRepository->getAll($limit, $whereConditions);
    }

    /**
     * @param int $id
     *
     * @return Administrator
     *
     * @throws NotFoundException
     */
    public function getAdministratorById(int $id)
    {
        $administrator = $this->administratorRepository->getById($id);
        if (!$administrator) {
            throw new NotFoundException(__('admin::adminCrud.adminNotFound'));
        }

        return $administrator;
    }

    /**
     * @param string $username
     *
     * @return \Illuminate\Database\Eloquent\Model|Administrator
     */
    public function getAdministratorByUsername(string $username)
    {
        return $this->administratorRepository->getAdministratorByUsername(
            $username
        );
    }

    /**
     * [addAvatar description]
     *
     * @param Administrator $admin
     * @param UploadedFile $file
     *
     * @return  bool
     */
    public function addAvatar(Administrator $admin, UploadedFile $file): bool
    {
        $avatarName = $this->storageService->uploadAvatar($admin, $file);
        $admin->update(['avatar' => $avatarName]);

        return true;
    }

    /**
     * [changeAvatar description]
     *
     * @param int $adminId
     * @param UploadedFile $file
     *
     * @return bool
     *
     * @throws NotFoundException
     */
    public function changeAvatar(int $adminId, UploadedFile $file): bool
    {
        $admin = $this->getAdministratorById($adminId);
        $avatarName = $this->storageService->uploadAvatar($admin, $file);
        $admin->update(['avatar' => $avatarName]);

        return true;
    }

    /**
     * [getDefaultAvatarPath description]
     *
     * @return string
     */
    public function getDefaultAvatarPath()
    {
        return StorageService::DEFAULT_AVATAR_PATH;
    }

    /**
     * @param Administrator $loggedAdministrator
     * @param Administrator $administrator
     * @param bool $throwException
     *
     * @return bool
     *
     * @throws ProblemException
     */
    public function canControl(
        Administrator $loggedAdministrator,
        Administrator $administrator,
        bool $throwException = true
    ) {
        $result = ($loggedAdministrator
                ->getMaxPriority() >= $administrator->getMaxPriority());

        if (!$result && $throwException) {
            throw new ProblemException('Access denied');
        }

        return $result;
    }
}
