<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\BankAccountRepository;
use Modules\Core\Services\BaseService;

class BackAccountService extends BaseService
{
    private BankAccountRepository $bankAccountRepository;

    /**
     * @param BankAccountRepository $bankAccountRepository
     */
    public function __construct(
        BankAccountRepository $bankAccountRepository
    ) {
        $this->bankAccountRepository = $bankAccountRepository;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
       return $this->bankAccountRepository->getAll();
    }

    /**
     * @param int $InvestorId
     *
     * @return mixed
     */
    public function getByInvestorId(int $InvestorId)
    {
       return $this->bankAccountRepository->getByInvestorId($InvestorId);
    }
}
