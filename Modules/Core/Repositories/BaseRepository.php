<?php

namespace Modules\Core\Repositories;

use Auth;

abstract class BaseRepository
{
    /**
     * @param array $order
     *
     * @return array
     */
    protected function prepareOrderStatement(array $order)
    {
        $orderByFields = [];
        array_walk(
            $order,
            function ($value, $key) use (&$orderByFields) {
                $orderByFields[] = $key . ' ' . $value;
            }
        );

        return $orderByFields;
    }

    /**
     * @param array $where
     * @param bool $showDeleted
     *
     * @return array
     */
    protected function checkForDeleted(array $where, bool $showDeleted, string $prefix = null)
    {
        if (!$showDeleted) {
            $where[] = [($prefix !== null ? $prefix . '.' : '') . 'deleted', '=', '0'];
        }

        return $where;
    }

    /**
     * @param array $joins
     * @param Illuminate\Database\Eloquent\Builder | Illuminate\Database\Query\Builder $builder
     *
     * @return Illuminate\Database\Eloquent\Builder|Illuminate\Database\Query\Builder
     */
    protected function setJoins(array $joins, $builder)
    {
        if (!empty($joins)) {
            foreach ($joins as $joinType => $joinVars) {
                foreach ($joinVars as $key => $joinArgs) {
                    $builder->{$joinType}(
                        $joinArgs[0], // reference table
                        $joinArgs[1], // reference column
                        $joinArgs[2],  // sign
                        $joinArgs[3]  // reference condition
                    );
                }
            }
        }

        return $builder;
    }

    /**
     * [getOrdersFromArray description]
     *
     * @param array $orders
     *
     * @return string
     */
    protected function getOrdersFromArray(array $orders): string
    {
        return implode(
            ', ',
            array_map(
                function ($v, $k) {
                    return $k . ' ' . $v;
                },
                $orders,
                array_keys($orders)
            )
        );
    }

    /**
     * [getOrdersFromArray description]
     *
     * @param array $orders
     *
     * @return string
     */
    protected function getWhereConditionsFromArray(array $where): string
    {
        return '(' . implode(
                ') AND (',
                array_map(
                    function ($row) {
                        return $row[0] . " " . $row[1] . " '" . $row[2] . "'";
                    },
                    $where
                )
            ) . ')';
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
