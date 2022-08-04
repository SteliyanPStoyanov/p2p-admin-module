<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\AffiliateStats;
use Modules\Core\Repositories\BaseRepository;

class AffiliateStatsRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return AffiliateStats
     */
    public function create(
        array $data
    ): AffiliateStats {
        $affiliate = new AffiliateStats();
        $affiliate->fill($data);
        $affiliate->save();

        return $affiliate;
    }
}
