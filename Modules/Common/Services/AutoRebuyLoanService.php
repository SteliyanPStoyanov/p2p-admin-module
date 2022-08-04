<?php

namespace Modules\Common\Services;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Common\Repositories\AutoRebuyRepository;
use Modules\Core\Services\BaseService;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Repositories\FileRepository;
use Modules\Common\Repositories\LoanRepository;
use Modules\Core\Services\StorageService;

class AutoRebuyLoanService extends BaseService
{
    protected AutoRebuyRepository $autoRebuyRepository;

    /**
     * AutoRebuyLoanService constructor.
     *
     * @param AutoRebuyRepository $autoRebuyRepository
     */
    public function __construct(
        AutoRebuyRepository $autoRebuyRepository
    ) {
        $this->autoRebuyRepository = $autoRebuyRepository;

        parent::__construct();
    }

    /**
     * @param $loan
     *
     * @return \Modules\Common\Entities\AutoRebuyLoan
     */
    public function createAutoRebuyLog(Loan $loan)
    {
        $params = [
            'loan_id' => $loan->loan_id,
            'remaining_principal' => $loan->remaining_principal,
            'overdue_days' => $loan->overdue_days,
        ];

        return $this->autoRebuyRepository->create($params);
    }

    /**
     * @param UnlistedLoan $unlistedLoan
     *
     * @return UnlistedLoan
     */
    public function markUnlistedLoanAsHandled(UnlistedLoan $unlistedLoan)
    {
        $unlistedLoan->handled = 1;

        $unlistedLoan->save();

        return $unlistedLoan;
    }
}
