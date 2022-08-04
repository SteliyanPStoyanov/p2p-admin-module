<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\InvestorContract;
use Modules\Common\Entities\LoanContract;
use Modules\Core\Repositories\BaseRepository;

class LoanContractRepository extends BaseRepository
{
    public function getById(int $id)
    {
        return LoanContract::where('loan_contract_id', $id)->first();
    }
}
