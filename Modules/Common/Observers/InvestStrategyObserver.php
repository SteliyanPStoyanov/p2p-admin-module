<?php

namespace Modules\Common\Observers;

use Auth;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\InvestStrategyHistory;
use Modules\Common\Services\InvestmentService;

class InvestStrategyObserver
{
    private $investmentService = null;

    /**
     * When strategy is created and it's agreed we do auto invest for it
     * @param InvestStrategy $strategy
     */
    public function created(InvestStrategy $strategy)
    {
        if (
            $strategy->isAgreed()
            // && $strategy->isActive() - OK BY DEFAULT, on creation we dont have this option
            // && !$strategy->isDeleted() - OK BY DEFAULT, on creation we dont have this option
        ) {
            $this->getInvestmentService()->massInvestByStrategy($strategy);
        }

        $this->getInvestmentService()->saveStrategyHistory(
            $strategy->getAttributes()
        );
    }

    /**
     * Before update on invest strategy we save current version into history
     * If active flag was changed to 1, we run the strategy
     * @param InvestStrategy $strategy
     */
    public function updating(InvestStrategy $strategy)
    {
        $history = $strategy->getAttributes();
        $history['archived_at'] = now();
        $history['archived_by'] = $this->getUserIdForArchive();
        $this->getInvestmentService()->saveStrategyHistory($history);

        // if strategy is editeed manually(activating flag)
        // or active state was changed to positive
        // we directly run the strategy
        if (
            ($strategy->isDirty('active') || $strategy->activating == 1)
            && $strategy->isActive()
            && $strategy->isAgreed()
            && !$strategy->isDeleted()
            && !$strategy->hasActiveBunches()
        ) {
            $this->getInvestmentService()->massInvestByStrategy($strategy);
        }

        // if strategy is stopped - stop all bunches
        if ($strategy->isDirty('active') && !$strategy->isActive()) {
            $strategy->stopAllBunches('strategy is stopped - stop all bunches');
        }

        // if strategy is deleted - stop all bunches
        if ($strategy->isDirty('deleted') && $strategy->isDeleted()) {
            $strategy->stopAllBunches('strategy is deleted - stop all bunches');
        }
    }

    private function getUserIdForArchive(): int
    {
        $user = Auth::guard('investor')->user();
        if (!empty($user->investor_id)) {
            return $user->investor_id;
        }

        return Administrator::SYSTEM_ADMINISTRATOR_ID;
    }

    private function getInvestmentService(): InvestmentService
    {
        if (null === $this->investmentService) {
            $this->investmentService = \App::make(InvestmentService::class);
        }

        return $this->investmentService;
    }
}
