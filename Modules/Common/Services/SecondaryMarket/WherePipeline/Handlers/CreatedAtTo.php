<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class CreatedAtTo implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            ['createdAt' => 'to'],
            ['created_at' => 'to'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if (!empty($value)) {
            $date = dbDate($value, '00:00:00');

            $marketSecondary->where('created_at', '<=', $date);

            $where[] = [
                'loan.created_at',
                '<=',
                $date,
            ];

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setMarketSecondary($marketSecondary);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
