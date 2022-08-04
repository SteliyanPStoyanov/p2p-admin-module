<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

class CreatedAtFrom implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            ['createdAt' => 'from'],
            ['created_at' => 'from'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        if (!empty($value)) {
            $where[] = [
                'loan.created_at',
                '>=',
                dbDate($value, '00:00:00'),
            ];

            $data = DataExtractor::unset($aliases, $data);
        }

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
