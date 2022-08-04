<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class IncludeInvested implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'include_invested',
            'my_investment',
        ];

        $value = DataExtractor::extract($aliases, $data);

        if ($value == 'exclude' || $value === '0') {
            $where['include_invested'][] = "not exists(select i.loan_id FROM investment as i WHERE loan.loan_id = i.loan_id AND i.investor_id = ?)";

            $where['include_invested']['whereRaw'] = 1;
        }

        $data = DataExtractor::unset($aliases, $data);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
