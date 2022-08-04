<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class InterestRateMin implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'min_interest_rate',
            ['interest_rate_percent' => 'from'],
        ];

        $value = DataExtractor::extract($aliases, $data);


        $marketSecondary = $dataWrapper->getMarketSecondary();

        // Workaround for cases when value is zero and not false
        if ($value >= 0 && $value != '') {
            $marketSecondary->whereHas('loan', function(Builder $query) use($value){
                $query->where('interest_rate_percent', '>=', $value );
            });

            $where[] = [
                'loan.interest_rate_percent',
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
