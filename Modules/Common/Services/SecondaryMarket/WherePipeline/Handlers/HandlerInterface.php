<?php

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

interface HandlerInterface
{
    /**
     * @param DataWrapper $dataWrapper
     * @param Closure $next
     * @return DataWrapper
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper;
}
