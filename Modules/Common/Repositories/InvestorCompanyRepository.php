<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\InvestorCompany;
use Modules\Core\Repositories\BaseRepository;

class InvestorCompanyRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return InvestorCompany
     */
    public function create(array $data): InvestorCompany
    {
        $investorCompany = new InvestorCompany();
        $investorCompany->fill($data);
        $investorCompany->save();

        return $investorCompany;
    }

}
