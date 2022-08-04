<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;


use Closure;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

class AmountAvailableMax implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'max_amount',
            ['amount_available' => 'to'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        if ($value) {
            $where[] = [
                'loan.amount_available',
                '<=',
                $value,
            ];

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
