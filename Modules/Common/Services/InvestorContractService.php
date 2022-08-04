<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\InvestorContractRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Services\BaseService;

class InvestorContractService extends BaseService
{
    protected InvestorContractRepository $investorContractRepository;

    /**
     * InvestorContractService constructor.
     *
     * @param InvestorContractRepository $investorContractRepository
     */
    public function __construct(InvestorContractRepository $investorContractRepository)
    {
        $this->investorContractRepository = $investorContractRepository;

        parent::__construct();
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function getById(int $id)
    {
        $investorContract = $this->investorContractRepository->getById($id);
        if (empty($investorContract)) {
            throw new NotFoundException(__('common.UserAgreementNotFound'));
        }

        return $investorContract;
    }
}
