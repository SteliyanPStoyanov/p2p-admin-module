<?php

namespace Modules\Common\Services;

use Modules\Common\Entities\Originator;
use Modules\Common\Repositories\OriginatorRepository;
use Modules\Core\Services\BaseService;

class OriginatorService extends BaseService
{
    protected OriginatorRepository $originatorRepository;

    public function __construct(OriginatorRepository $originatorRepository)
    {
        $this->originatorRepository = $originatorRepository;

        parent::__construct();
    }

    /**
     * @param int $id
     *
     * @return null|Originator
     */
    public function getById(int $id): ?Originator
    {
        return $this->originatorRepository->getById($id);
    }
}
