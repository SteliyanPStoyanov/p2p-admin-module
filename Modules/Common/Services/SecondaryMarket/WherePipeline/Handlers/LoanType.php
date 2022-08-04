<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class LoanType implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'loan_type',
            'type', // admin
            ['loan' => 'type'],
        ];

        $value = DataExtractor::extractArray($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if ($value) {
            $marketSecondary->whereHas('loan', function(Builder $query) use($value){
                $query->whereIn('type', $value );
            });

            foreach ($value as $type) {
                if(is_array($type)) {
                    $where['loan.type'] = $type;
                    continue;
                }

                $where['loan.type'][] = $type;
            }

            // Mark it to be processed as WHERE IN()
            $where['loan.type']['whereIn'] = 1;

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setMarketSecondary($marketSecondary);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
