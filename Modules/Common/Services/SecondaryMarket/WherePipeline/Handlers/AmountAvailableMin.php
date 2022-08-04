<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class AmountAvailableMin implements HandlerInterface
{

    public const MIN_VALUE = 0;
    public const ADMIN_MIN_VALUE = 0;

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'min_amount',
            ['amount_available' => 'from'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if ($value && ! $dataWrapper->isAdmin()) {
            if ($value < self::MIN_VALUE) {
                $value = self::MIN_VALUE;
            }

            $marketSecondary->where('principal_for_sale', '>=', $value);

            $where[] = [
                'loan.amount_available',
                '>=',
                $value,
            ];
        } elseif ($dataWrapper->isAdmin()) {
            $marketSecondary->where('principal_for_sale', '>=', self::ADMIN_MIN_VALUE);

            $where[] = [
                'loan.amount_available',
                '>=',
                self::ADMIN_MIN_VALUE,
            ];
        } else {
            $marketSecondary->where('principal_for_sale', '>=', self::MIN_VALUE);

            $where[] = [
                'market_secondary.principal_for_sale',
                '>=',
                self::MIN_VALUE,
            ];
        }

        $dataWrapper->setMarketSecondary($marketSecondary);

        $data = DataExtractor::unset($aliases, $data);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
