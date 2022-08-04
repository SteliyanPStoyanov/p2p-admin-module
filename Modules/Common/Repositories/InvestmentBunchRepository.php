<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\InvestmentBunch;
use Modules\Core\Repositories\BaseRepository;

class InvestmentBunchRepository extends BaseRepository
{

    /**
     * @param int $investmentBunchId
     *
     * @return mixed
     */
    public function getById(int $investmentBunchId)
    {
      return InvestmentBunch::where('investment_bunch_id', $investmentBunchId)->first();
    }

    /**
     * @param array $data
     *
     * @return InvestmentBunch
     */
    public function create(array $data)
    {
        $investmentBunch = new InvestmentBunch();
        $investmentBunch->fill($data);
        $investmentBunch->save();

        return $investmentBunch;
    }

    /**
     * @param InvestmentBunch $investmentBunch
     * @param array $data
     *
     * @return InvestmentBunch
     */
    public function update(InvestmentBunch $investmentBunch ,array $data)
    {
        $investmentBunch->fill($data);
        $investmentBunch->save();

        return $investmentBunch;
    }
}
