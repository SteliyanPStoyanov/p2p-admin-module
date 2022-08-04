<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;


use Closure;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class DiscountPremiumMin implements HandlerInterface
{
    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            ['discount' => 'from'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if (!empty($value)) {
            $marketSecondary->where('premium', '>=', $value);

            $where[] = [
                'market_secondary.premium',
                '>=',
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
