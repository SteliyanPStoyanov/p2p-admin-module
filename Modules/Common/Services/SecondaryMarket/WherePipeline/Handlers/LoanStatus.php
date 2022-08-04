<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class LoanStatus implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            ['payment_status' => 'range1'],
            ['payment_status' => 'range2'],
            ['payment_status' => 'range3'],
            ['payment_status' => 'range4'],
        ];

        $specialAliases = [
            'range1' => Loan::PAY_STATUS_CURRENT,
            'range2' => Loan::PAY_STATUS_1_15,
            'range3' => Loan::PAY_STATUS_16_30,
            'range4' => Loan::PAY_STATUS_31_60,
        ];

        $rawValue = DataExtractor::extractMultipleValues($aliases, $data);

        $value = [];
        foreach ($rawValue as $v) {
            $value[] = $specialAliases[$v];
        }

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if (!empty($value)) {
            $marketSecondary->whereHas('loan', function(Builder $query) use($value){

                $query->whereIn('payment_status', $value);
            });

            $where[] = [
                'loan.payment_status',
                '=',
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
