<?php


namespace Modules\Common\Services;


use Illuminate\Support\Collection;
use Modules\Common\Repositories\InvestorBonusRepository;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;

class InvestorBonusService extends BaseService
{

    private InvestorBonusRepository $investorBonusRepository;

    /**
     * InvestorService constructor.
     *
     * @param InvestorBonusRepository $investorBonusRepository
     */

    public function __construct(InvestorBonusRepository $investorBonusRepository)
    {
        $this->investorBonusRepository = $investorBonusRepository;

        parent::__construct();
    }

    /**
     * @param int $investorBonusId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|\Modules\Common\Entities\InvestorBonus[]
     * @throws ProblemException
     */
    public function getById(int $investorBonusId)
    {
        if (!$investorBonus = $this->investorBonusRepository->getById($investorBonusId)) {
            throw new ProblemException(__('common.InvestorBonusNotFound'));
        }

        return $investorBonus;
    }

    /**
     * @return Collection
     */
    public function getInvestorsUnhandledBonus(): Collection
    {
      return  $this->investorBonusRepository->getUnhandledBonus();
    }

}
