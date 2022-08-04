<?php

namespace Modules\Core\Controllers;

use \ReflectionClass;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Modules\Common\Repositories\InvestorRepository;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\SessionService;
use Modules\Core\Services\StorageService;

class BaseController extends Controller
{
    const DEFAULT_TABLE_ROWS_COUNT = 10;
    const PAGE_TYPE_LIST = 'list';
    const PAGE_TYPE_CREATE = 'create';
    const PAGE_TYPE_EDIT = 'edit';
    const PAGE_TYPE_DELETE = 'delete';

    private ?SessionService $sessionService = null;
    protected ?StorageService  $storageService = null;
    protected ?InvestorRepository  $investorRepository = null;
    protected ?CacheService $cacheService = null;
    protected string $cacheKey = '';
    protected string $pageTitle = '';
    protected ?string $keyForCachedData = null;

    /**
     * BaseController constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->setCacheKey();
        $this->setPageTitle($this->pageTitle);
    }

    /**
     * @return StorageService
     */
    public function getStorageService()
    {
        if ($this->storageService === null) {
            $this->storageService = App::make(StorageService::class);
        }

        return $this->storageService;
    }

    /**
     * @return CacheService
     */
    public function getCacheService(): CacheService
    {
        if ($this->cacheService === null) {
            $this->cacheService = App::make(CacheService::class);
        }

        return $this->cacheService;
    }

    /**
     * @param $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        view()->share('pageTitle', $pageTitle);
    }

    /**
     * @throws \ReflectionException
     */
    public function setCacheKey()
    {
        if (empty($this->cacheKey)) {
            $class = (new ReflectionClass($this))->getShortName();
            $this->cacheKey = sprintf(
                "%s.%s.%s",
                'filters',
                strtolower(str_replace('Controller', '', $class)),
                'list'
            );
        }
    }

    /**
     * @param $page
     * @param $action
     *
     * @return string
     */
    public function setPageTitleDynamicaly($page, $action)
    {
        if (self::PAGE_TYPE_LIST == $action) {
            return ucfirst($page) . ' ' . $action;
        }

        return ucfirst($action) . ' ' . $page;
    }

    /**
     * @param $class
     * @param string $method
     *
     * @return bool
     *
     * @throws \ReflectionException
     */
    public function hasParams($class, string $method): bool
    {
        $method = new \ReflectionMethod($class, $method);
        return !empty($method->getParameters());
    }

    /**
     * @return mixed
     */
    public function getFilters($cachekey = null)
    {
        $key = !empty($cachekey) ? $cachekey : $this->cacheKey;
        return $this->getSessionService()->get($key);
    }

    /**
     * @return bool
     */
    public function cleanFilters()
    {
        return $this->getSessionService()->remove($this->cacheKey);
    }

    /**
     * @param BaseRequest $request
     *
     * @param null $cacheKey
     *
     * @return bool
     */
    public function setFiltersFromRequest(BaseRequest $request, $cacheKey = null)
    {
        $key = !empty($cacheKey) ? $cacheKey : $this->cacheKey;

        return $this->getSessionService()->add(
            $key,
            $request->validated()
        );
    }

    /**
     * [getSessionService description]
     *
     * @return SessionService
     */
    protected function getSessionService()
    {
        if (null === $this->sessionService) {
            $this->sessionService = new SessionService();
        }

        return $this->sessionService;
    }

    /**
     * @return int
     */
    public function getTableLength()
    {
        return intval(session($this->cacheKey . '.length', self::DEFAULT_TABLE_ROWS_COUNT));
    }

    /**
     * @return null|\Modules\Common\Entities\Investor
     */
    public function getInvestor()
    {
        $this->investorRepository = new InvestorRepository();
        return $this->investorRepository->getById(Auth::guard('investor')->user()->investor_id);
    }

    /**
     * @return int
     */
    public function getInvestorId() : int
    {
        return $this->getInvestor()->investor_id;
    }

    /**
     * @return string
     */
    protected function getKeyForCache()
    {
        if ($this->keyForCachedData == null) {
            $sessionKeys = session($this->cacheKey, []);
            $sessionKeys['page'] = request()->get('page');
            $sessionKeys['controller'] = static::class;
            $this->keyForCachedData = md5(json_encode($sessionKeys));
        }

        return $this->keyForCachedData;
    }

    /**
     * @param string $className
     *
     * @return mixed
     */
    protected function getCachedData(string $className): ?LengthAwarePaginator
    {
        $cachedData = $this->getCacheService()->get($this->getKeyForCache());
        if (empty($cachedData)) {
            return null;
        }

        return new LengthAwarePaginator(
            $className::hydrate($cachedData->data),
            $cachedData->total,
            $cachedData->per_page,
            $cachedData->current_page,
        );
    }

    /**
     * @param $data
     * @param ?int $timeout
     */
    protected function setCacheData(
        $data,
        $timeout = null
    ) {
        if (!is_int($timeout)) {
            $timeout = config('cache.cacheListTimeOut');
        }

        $this->getCacheService()->set($this->getKeyForCache(), json_encode($data), $timeout);
    }
}
