<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class LoanPeriodMax implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            ['period' => 'to'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if (!empty($value)) {
            $value = (int)$value;
            $max_loan_period = dbDate(Carbon::now()->addMonths($value));

            $marketSecondary->whereHas('loan', function(Builder $query) use($max_loan_period){
                $query->where('final_payment_date', '<=', $max_loan_period );
            });

            $where[] = [
                'loan.final_payment_date',
                '<=',
                $max_loan_period,
            ];

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setMarketSecondary($marketSecondary);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
