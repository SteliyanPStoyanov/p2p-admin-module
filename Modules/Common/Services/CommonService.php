<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\TransactionRepository;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\PortfolioService;

class CommonService
{
    protected $investorService = null;
    protected $portfolioService = null;
    protected $transactionRepo = null;
    protected $transactionService = null;

    protected function getInvestorService()
    {
        if (null === $this->investorService) {
            $this->investorService = \App::make(InvestorService::class);
        }

        return $this->investorService;
    }

    protected function getPortfolioService()
    {
        if (null === $this->portfolioService) {
            $this->portfolioService =  \App::make(PortfolioService::class);
        }

        return $this->portfolioService;
    }

    protected function getTransactionRepo()
    {
        if (null === $this->transactionRepo) {
            $this->transactionRepo =  \App::make(TransactionRepository::class);
        }

        return $this->transactionRepo;
    }

    protected function getTransactionService()
    {
        if (null === $this->transactionService) {
            $this->transactionService =  \App::make(TransactionService::class);
        }

        return $this->transactionService;
    }
}
