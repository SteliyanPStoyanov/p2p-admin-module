<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\Originator;
use Modules\Core\Repositories\BaseRepository;

class OriginatorRepository extends BaseRepository
{
    /**
     * @param int $id
     *
     * @return Originator|null
     */
    public function getById(int $id): ?Originator
    {
        return Originator::where('originator_id', $id)->first();
    }
}
