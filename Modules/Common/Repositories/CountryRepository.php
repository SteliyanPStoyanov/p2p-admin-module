<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\Country;
use Modules\Core\Repositories\BaseRepository;

class CountryRepository extends BaseRepository
{
    /**
     * @return mixed
     */
    public function getAll()
    {
        return Country::where(
                'active',
                '=',
                '1'
            )
            ->orderBy('name', 'ASC')
            ->get();
    }
}
