<?php
declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline\Handlers;

use Closure;
use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

class AmountAvailableMin implements HandlerInterface
{

    public const MIN_VALUE = 10;
    public const ADMIN_MIN_VALUE = 0;

    /**
     * @inheritDoc
     */
    public function handle(DataWrapper $dataWrapper, Closure $next): DataWrapper
    {
        $data = $dataWrapper->getData();
        $where = $dataWrapper->getWhere();

        $aliases = [
            'min_amount',
            ['amount_available' => 'from'],
        ];

        $value = DataExtractor::extract($aliases, $data);

        if ($value && ! $dataWrapper->isAdmin()) {
            if ($value < self::MIN_VALUE) {
                $value = self::MIN_VALUE;
            }

            $where[] = [
                'loan.amount_available',
                '>=',
                $value,
            ];
        } elseif ($dataWrapper->isAdmin()) {
            $where[] = [
                'loan.amount_available',
                '>=',
                self::ADMIN_MIN_VALUE,
            ];
        } else {
            $where[] = [
                'loan.amount_available',
                '>=',
                self::MIN_VALUE,
            ];
        }

        $data = DataExtractor::unset($aliases, $data);

        $dataWrapper->setData($data);
        $dataWrapper->setWhere($where);

        return $next($dataWrapper);
    }
}
