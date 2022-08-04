<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\Affiliate;
use Modules\Core\Repositories\BaseRepository;

class AffiliateRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return Affiliate
     */
    public function create(
        array $data
    ): Affiliate {
        $affiliate = new Affiliate();
        $affiliate->fill($data);
        $affiliate->save();

        return $affiliate;
    }
}
