<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorAgreement;
use Modules\Core\Repositories\BaseRepository;

class AgreementRepository extends BaseRepository
{
    /**
     * @param Investor $investor
     * @param int $agreementId
     * @param int $value
     *
     * @return InvestorAgreement
     */
    public function addInvestorAgreement(
        Investor $investor,
        int $agreementId,
        int $value
    ): InvestorAgreement {
        $investorAgreement = new InvestorAgreement();
        $investorAgreement->fill(
            [
                'agreement_id' => $agreementId,
                'investor_id' => $investor->investor_id,
                'value' => $value,
            ]
        );
        $investorAgreement->save();

        return $investorAgreement;
    }

    /**
     * @param Investor $investor
     * @param int $agreementId
     * @param int $value
     *
     * @return InvestorAgreement
     */
    public function refreshAgreement(
        Investor $investor,
        int $agreementId,
        int $value
    ): InvestorAgreement {
        return InvestorAgreement::updateOrCreate(
            ['investor_id' => $investor->investor_id, 'agreement_id' => $agreementId],
            ['value' => $value]
        );
    }
}
