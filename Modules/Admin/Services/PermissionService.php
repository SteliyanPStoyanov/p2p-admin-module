<?php

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\PermissionRepository;
use \Modules\Core\Services\BaseService;

class PermissionService extends BaseService
{
    /**
     * @var PermissionRepository
     */
    protected PermissionRepository $permissionRepository;

    /**
     * PermissionService constructor.
     *
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(
        PermissionRepository $permissionRepository
    ) {
        $this->permissionRepository = $permissionRepository;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->permissionRepository->getAll();
    }
}
