<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Closure;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

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
            'status',
        ];

        $value = DataExtractor::extract($aliases, $data);

        if (!empty($value)) {
            if ($data['status'] == Loan::STATUS_ACTIVE) {
                $where[] = [
                    'loan.status',
                    '=',
                    $data['status'],
                ];
            }
            elseif ($data['status'] == Loan::STATUS_REPAID_EARLY || $data['status'] == Loan::STATUS_REPAID || $data['status'] == Loan::STATUS_REBUY) {
                $where[] = [
                    'loan.status',
                    '=',
                    $data['status'],
                ];
            }
            else {
                $where['loan.status'] = [
                    Loan::STATUS_REPAID_EARLY,
                    Loan::STATUS_REPAID,
                    Loan::STATUS_REBUY,
                ];
                $where['loan.status']['whereIn'] = 1;
            }

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
