<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Closure;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

class LoanPaymentStatus implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'loan_payment_status',
            'payment_status',
        ];

        $value = DataExtractor::extractArray($aliases, $data);

        if (!empty($value)) {
            foreach ($value as $range) {
                if(strpos($range, 'range') !== false)
                {
                    $range = Portfolio::getQualityMapping($range);
                }

                $where['loan.payment_status'][] = $range;
            }

            // Mark it to be processed as WHERE IN()
            $where['loan.payment_status']['whereIn'] = 1;

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
