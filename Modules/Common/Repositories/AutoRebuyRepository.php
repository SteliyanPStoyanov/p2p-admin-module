<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\AutoRebuyLoan;
use Modules\Core\Repositories\BaseRepository;

class AutoRebuyRepository extends BaseRepository
{
    /**
     * @param array $data
     *
     * @return AutoRebuyLoan
     */
    public function create(array $data)
    {
        $autoRebuyLoan = new AutoRebuyLoan();
        $autoRebuyLoan->fill($data);
        $autoRebuyLoan->save();

        return $autoRebuyLoan;
    }
}
