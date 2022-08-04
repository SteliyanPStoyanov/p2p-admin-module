<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

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


        if ($value) {
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

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
