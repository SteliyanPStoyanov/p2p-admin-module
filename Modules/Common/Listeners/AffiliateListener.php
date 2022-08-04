<?php

namespace Modules\Common\Listeners;


use Cookie;
use Exception;
use Log;
use Modules\Common\Affiliates\AffiliateUrl;
use Modules\Common\Entities\Affiliate;
use Modules\Common\Events\AffiliateEvents;
use Modules\Common\Repositories\AffiliateInvestorRepository;
use Modules\Common\Repositories\AffiliateRepository;
use Modules\Core\Services\CacheService;

class AffiliateListener
{
    /**
     * @param AffiliateEvents $affiliateEvents
     * @return false
     */
    public function handle(AffiliateEvents $affiliateEvents)
    {
        try {
            $affiliateUrl = AffiliateUrl::fromString($affiliateEvents->fullUrl);

            if (
                $affiliateUrl->getAllQueryParameters() !== null
                && !empty($affiliateUrl->getAllQueryParameters())
            ) {
                $queryParameters = $affiliateUrl->getAllQueryParameters();
            }

            if (!empty($queryParameters['utm_source']) && self::checkAffiliateExist($queryParameters) === false) {
                $source = $queryParameters['utm_source'];

                if (!array_key_exists($source, Affiliate::AFFILIATE_SOURCE)) {
                    throw new Exception('This affiliate source not exists !');
                }

                $className = Affiliate::AFFILIATE_SOURCE[$source];
                $service = new $className($queryParameters);

                $affiliateRepository = new AffiliateRepository();
                $affiliate = $affiliateRepository->create(
                    [
                        'affiliate_description' => json_encode($queryParameters)
                    ]
                );

                $affiliateInvestorRepository = new AffiliateInvestorRepository();
                $affiliateInvestorRepository->create(
                    [
                        'client_id' => $service->getClientId(),
                        'affiliate_id' => $affiliate->affiliate_id
                    ]
                );

                (new CacheService)->set(
                    'affiliate',
                     json_encode(
                        [
                            'name' => $source,
                            'client_id' => $service->getClientId(),
                            'created_at' => $affiliate->created_at,
                        ]
                    ),
                    60
                );
            }
        } catch (Exception $e) {
            Log::channel('affiliate')->error(
                'Failed to save change log. ' . $e->getMessage()
            );
            return false;
        }
    }

    /**
     * @return false
     */
    public function checkAffiliateExist($queryParameters): bool
    {
        $affiliateInvestorRepository = new AffiliateInvestorRepository();

        if ($queryParameters) {
            return $affiliateInvestorRepository->isExists($queryParameters['v']);
        }

        return false;
    }
}

