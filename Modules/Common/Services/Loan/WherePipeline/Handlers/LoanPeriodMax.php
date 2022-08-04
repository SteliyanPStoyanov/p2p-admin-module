<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Carbon\Carbon;
use Closure;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

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
            'max_loan_period',
            ['period' => 'to'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        if (!empty($value)) {
            $value = (int)$value;

            $where[] = [
                'loan.final_payment_date',
                '<=',
                dbDate(Carbon::now()->addMonths($value)),
            ];


            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
