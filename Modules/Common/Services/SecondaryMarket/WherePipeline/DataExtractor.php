<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline;

// TODO: Refactor this class. Split extract conditions in separate functions - this way it will become testable to details

class DataExtractor
{
    public static function extract(array $aliases, array $data): string
    {
        $value = '';
        foreach ($aliases as $k => $v) {
            if(
                is_string($v) &&
                isset($data[$v]) &&
                $data[$v] != '' &&
                $data[$v] >= 0 &&
                $data[$v] <= 0
            ) {
                return self::extractString($data, $v);
            }

            if(is_string($v) && ! empty($data[$v])) {
                $value =  self::extractString($data, $v);
                if(! empty($value)) {
                    break;
                }
            }

            if(is_array($v)) {
                $value = self::extractStringFromSubArray($data, $v);
                if(! empty($value)) {
                    break;
                }
            }
        }

        return $value;
    }

    public static function extractArray(array $aliases, array $data): array
    {
        foreach ($aliases as $k => $v) {
            if(is_string($v) && ! empty($data[$v])) {
                return self::extractArraySimple($data, $v);
            }

            if(is_array($v)) {
                return self::extractArrayFromSubArray($data, $v);
            }
        }

        return [];
    }

    public static function extractMultipleValues(array $aliases, array $data): array
    {
        $values = [];
        foreach ($aliases as $item) {
            foreach ($item as $k => $alias) {
                if (isset($data[$k][$alias]) && $data[$k][$alias]) {
                    $values[] = $data[$k][$alias];
                    echo $data[$k][$alias] . PHP_EOL;
                }
            }
        }

        return $values;
    }

    public static function unsetMultiple(array $aliases, array $data): array
    {
        foreach ($aliases as $item) {
            foreach ($item as $k => $alias) {
                if (isset($data[$k][$alias]) && $data[$k][$alias]) {
                    unset($data[$k]);
                }
            }
        }

        return $data;
    }

    public static function unset(array $aliases, array $data): array
    {
        foreach ($aliases as $k => $v) {
            if (is_string($v)) {
                if(isset($data[$v])) {
                    unset($data[$v]);

                    return $data;
                }
            }

            if(is_array($v)) {
                if(
                    isset($data[key($v)]) &&
                    isset($data[key($v)][$v[key($v)]])
                ) {
                    unset($data[key($v)][$v[key($v)]]);

                    return $data;
                }
            }
        }

        return $data;
    }

    public static function extractStringFromSubArray(array $data, array $v): string
    {
        if(
            isset($data[key($v)]) &&
            isset($data[key($v)][$v[key($v)]]) &&
            ! empty($data[key($v)][$v[key($v)]])
        ) {
            return (string)$data[key($v)][$v[key($v)]];
        }

        return '';
    }

    public static function extractString(array $data, string $v): string
    {
        // special case for IncludeInvested, InterestRateMin, and LoanPeriodMin
        if (isset($data[$v]) && $data[$v] >= 0 && $data[$v] <= 0 ) { // workaround to make php treat zero as a value and not as false
            return (string)$data[$v];
        }

        if(isset($data[$v]) && ! empty($data[$v])) {
            return (string)$data[$v];
        }

        return '';
    }

    public static function extractArraySimple(array $data, string $v): array
    {
        if(isset($data[$v]) && ! empty($data[$v])) {
            if (is_array($data[$v])) {
                return $data[$v];
            }

            return [
                $data[$v]
            ];
        }

        return [];
    }

    public static function extractArrayFromSubArray(array $data, array $v): array
    {
        if(
            isset($data[key($v)]) &&
            isset($data[key($v)][$v[key($v)]]) &&
            ! empty($data[key($v)][$v[key($v)]])
        ) {
            return $data[key($v)];
        }

        return [];
    }
}
