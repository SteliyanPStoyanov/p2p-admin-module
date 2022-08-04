<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\InvestorContract;
use Modules\Core\Repositories\BaseRepository;

class InvestorContractRepository extends BaseRepository
{
    public function getById(int $id)
    {
        return InvestorContract::where('investor_contract_id', $id)->first();
    }
}
