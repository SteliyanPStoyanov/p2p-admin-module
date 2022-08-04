<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\CountryRepository;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\CacheService;

class CountryService extends BaseService
{
    private CountryRepository $countryRepository;
    private CacheService $cacheService;

    /**
     * CountryService constructor.
     * @param CountryRepository $countryRepository
     * @param CacheService $cacheService
     */
    public function __construct(
        CountryRepository $countryRepository,
        CacheService $cacheService

    ) {
        $this->countryRepository = $countryRepository;
        $this->cacheService = $cacheService;
        parent::__construct();
    }


    public function getAll()
    {
        $cacheKey = 'all_countries';

        if ($this->cacheService->get($cacheKey) == null) {
            $this->cacheService->set(
                $cacheKey,
                $this->countryRepository->getAll(),
                600
            );
        }

        return $this->cacheService->get($cacheKey);
    }
}
