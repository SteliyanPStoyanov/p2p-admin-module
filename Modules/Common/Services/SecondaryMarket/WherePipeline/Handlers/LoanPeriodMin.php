<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataExtractor;
use Modules\Common\Services\SecondaryMarket\WherePipeline\DataWrapper;

class LoanPeriodMin implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            ['period' => 'from'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        $marketSecondary = $dataWrapper->getMarketSecondary();

        if ($value) {
            $value = (int)$value;
            $min_loan_period = dbDate(Carbon::now()->addMonths($value));

            // Left it here in case we will need to check for zero in future
//            if ($value >= 0 && $value <= 0) {
//                $min_loan_period = dbDate(Carbon::now());
//            }

            $marketSecondary->whereHas('loan', function(Builder $query) use($min_loan_period){
                $query->where('final_payment_date', '>=', $min_loan_period );
            });

            $where[] = [
                'loan.final_payment_date',
                '>=',
                $min_loan_period,
            ];

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setMarketSecondary($marketSecondary);

        // Make sure we unset it even if it exist and == 0
        $data = DataExtractor::unset($aliases, $data);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
