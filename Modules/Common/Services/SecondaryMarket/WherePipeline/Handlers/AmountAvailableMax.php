<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;


use Closure;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class AmountAvailableMax implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'max_amount',
            ['amount_available' => 'to'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if ($value) {
            $marketSecondary->where('principal_for_sale', '<=', $value);

            $where[] = [
                'market_secondary.principal_for_sale',
                '<=',
                $value,
            ];

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setMarketSecondary($marketSecondary);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
