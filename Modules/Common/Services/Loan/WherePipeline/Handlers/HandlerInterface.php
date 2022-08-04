<?php

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

interface HandlerInterface
{
    /**
     * @param DataWrapper $dataWrapper
     * @param Closure $next
     * @return DataWrapper
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper;
}
