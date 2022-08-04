<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\InvestorContractRepository;
use Modules\Common\Repositories\LoanContractRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Services\BaseService;

class LoanContractService extends BaseService
{
    protected LoanContractRepository $loanContractRepository;

    /**
     * LoanContractService constructor.
     *
     * @param LoanContractRepository $loanContractRepository
     */
    public function __construct(LoanContractRepository $loanContractRepository)
    {
        $this->loanContractRepository = $loanContractRepository;

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
        $loanContract = $this->loanContractRepository->getById($id);
        if (empty($loanContract)) {
            throw new NotFoundException(__('common.UserAgreementNotFound'));
        }

        return $loanContract;
    }
}
