<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\AffiliateInvestor;
use Modules\Core\Repositories\BaseRepository;

class AffiliateInvestorRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return AffiliateInvestor
     */
    public function create(
        array $data
    ): AffiliateInvestor {
        $affiliateInvestor = new AffiliateInvestor();
        $affiliateInvestor->fill($data);
        $affiliateInvestor->save();

        return $affiliateInvestor;
    }

    /**
     * @param int $investorId
     * @param $clientId
     */
    public function update(int $investorId, $clientId)
    {
        AffiliateInvestor::where(
            [
                [
                    'client_id',
                    '=',
                    $clientId
                ]
            ]
        )->update(['investor_id' => $investorId]);
    }

    /**
     * @param string $clientId
     * @return bool
     */
    public function isExists(string $clientId): bool
    {
        return (AffiliateInvestor::where(
                [
                    'client_id' => $clientId,
                ]
            )->count()) > 0;
    }
}
