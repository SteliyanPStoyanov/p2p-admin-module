<?php
namespace Modules\Core\Services;

use DB;
use Modules\Core\Traits\DateBuilderTrait;
use Modules\Core\Traits\DynamicLazyLoader;
use Modules\Core\Traits\StringFormatterTrait;

class BaseService
{
    use DateBuilderTrait;
    use DynamicLazyLoader;
    use StringFormatterTrait;

    public const CONCAT_KEY = 'name';
    public const STATUS_KEY = 'status';
    public const STATUS_KEYS = [
        'active',
        'blocked',
        'deleted'
    ];
    public function __construct()
    {
    }
    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    )
    {
        $where = [];
        foreach ($data as $key => $item) {
            if (!isset($item)) {
                continue;
            }

            if (!is_string($item) && !is_numeric($item)) {
                continue;
            }

            if ($key == self::STATUS_KEY && in_array($item, self::STATUS_KEYS)) {
                $where[] = [
                    $prefix . ($prefix != '' ? '.' : '') . $item,
                    '=',
                    '1',
                ];
                continue;
            }
            if (self::CONCAT_KEY === $key && !empty($names)) {
                $where[] = [
                    DB::raw("CONCAT_WS(' ', " . implode(', ', $names) . ")"),
                    'ILIKE',
                    "%{$item}%",
                ];
                continue;
            }

            if (is_numeric($item) && !preg_match('/pin|phone|value/', $key)) {
                $where[] = [
                    $prefix . ($prefix != '' ? '.' : '') . $this->fmtCamelCaseToSnakeCase($key),
                    '=',
                    "{$item}",
                ];
                continue;
            }
            if (preg_match(self::$dateRangeRegex, $item)) {
                $extractedDates = $this->extractDates($item);
                $where[] = [
                    $prefix . ($prefix != '' ? '.' : '') .$this->fmtCamelCaseToSnakeCase($key),
                    '>=',
                    $extractedDates['from'],
                ];
                $where[] = [
                    $prefix . ($prefix != '' ? '.' : '') .$this->fmtCamelCaseToSnakeCase($key),
                    '<=',
                    $extractedDates['to'],
                ];
                continue;
            }
            $where[] = [
                $prefix . ($prefix != '' ? '.' : '') .$this->fmtCamelCaseToSnakeCase($key),
                'ILIKE',
                "%{$item}%"
            ];
        }

        return $where;
    }
    /**
     * [adminCanChangeOffice description]
     * @param  int    $officeId
     * @return bool
     */
    public function adminCanChangeOffice(int $officeId): bool
    {
        return $this->getAdministratorRepository('Modules\\Admin\\Repositories\\')
            ->adminBelongsToOffice($officeId);
    }
    /**
     * [adminBelongsToClientOffices description]
     * @param  array    $officeIds
     * @return bool
     */
    public function adminBelongsToClientOffices(array $officeIds): bool
    {
        return $this->getAdministratorRepository('Modules\\Admin\\Repositories\\')
            ->adminBelongsToOffices($officeIds);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getOrderConditions(
        array $data
    ) {
        $order = [];
        if (!empty($data['order'])) {
            foreach ($data['order'] as $key => $variable) {
                if (is_array($variable)) {
                    foreach ($variable as $keySub => $variableSub) {
                        $order = [
                            $key.'.'.$keySub => strtoupper($variableSub),
                        ];
                    }
                } else {
                    $order = [
                        $key => strtoupper($variable),
                    ];
                }
            }
        }
        return $order;
    }
}
