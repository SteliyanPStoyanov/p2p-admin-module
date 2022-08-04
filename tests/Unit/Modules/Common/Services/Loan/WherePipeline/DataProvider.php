<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline;


use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;

class DataProvider
{
    /**
     * @return \array[][]
     */
    public static function getData(): array
    {
        return [
            'testSet1' => [
                [
                    'max_amount' => 100,
                    'amount_available' => [
                        'to' => 101,
                        'from' => 11,
                    ],
                    'min_amount' => 10,
                    'created_at' => [
                        'from' => '08.03.2021',
                        'to' => '09.03.2021',
                    ],
                    'include_invested' => '0',
                    'my_investment' => 'exclude',
                    'min_interest_rate' => 15,
                    'max_interest_rate' => 18,
                    'interest_rate_percent' => [
                        'from' => 15,
                        'to' => 18
                    ],
                    'loan_payment_status' => [
                        'current',
                        '1-15 days'
                    ],
                    'payment_status' => [
                        'range1' => 'range1',
                        'range2' => 'range2',
                    ],
                    'min_loan_period' => 1,
                    'max_loan_period' => 10,
                    'period' => [
                        'from' => 1,
                        'to' => 10,
                    ],
                    'loan_type' => [
                        'payday',
                        'installments',
                    ],
                    'loan' => [
                        'type' => 'installments'
                    ],
                    'test' => [
                        'sub' => [
                            'array' => 1
                        ]
                    ],
                ]
            ]
        ];
    }

    public static function forHandlers(Builder $builder, array $data): array
    {
        $dataWrapper = new DataWrapper($builder, $data, 111);

        $closure = function($dataWrapper) {
            return $dataWrapper;
        };

        return [
                $dataWrapper,
                $closure
        ];
    }


    public static function forAdminHandlers(Builder $builder, array $data): array
    {
        $dataWrapper = new DataWrapper($builder, $data, 111, true);

        $closure = function($dataWrapper) {
            return $dataWrapper;
        };

        return [
                $dataWrapper,
                $closure
        ];
    }

    /**
     * @return array
     */
    public static function getRawData(): array
    {
        return [
            'max_amount' => 100,
            'amount_available' => [
                'from' => 11,
                'to' => 101,
            ],
            'min_amount' => 10,
            'created_at' => [
                'from' => '08.03.2021',
                'to' => '09.03.2021',
            ],
            'include_invested' => '0',
            'my_investment' => 'exclude',
            'min_interest_rate' => 15,
            'max_interest_rate' => 18,
            'interest_rate_percent' => [
                'from' => 15,
                'to' => 18
            ],
            'loan_payment_status' => [
                'current',
                '1-15 days'
            ],
            'payment_status' => [
                'range1' => 'range1',
                'range2' => 'range2',
            ],
            'min_loan_period' => 1,
            'max_loan_period' => 10,
            'period' => [
                'from' => 1,
                'to' => 10,
            ],
            'loan_type' => [
                'payday',
                'installments',
            ],
            'loan' => [
                'type' => 'installments'
            ],
            'test' => [
                'sub' => [
                    'array' => 1
                ]
            ],
        ];
    }


    /**
     * @return \array[][]
     */
    public static function getDataVariantAutoInvest(): array
    {
        return [
            'max_amount' => 100,
            'min_amount' => 10,
            'include_invested' => '0',
            'min_interest_rate' => 15,
            'max_interest_rate' => 18,
            'loan_payment_status' => [
                'current',
                '1-15 days'
            ],
            'min_loan_period' => 1,
            'max_loan_period' => 10,
            'loan_type' => [
                'payday',
                'installments',
            ],
        ];
    }

    /**
     * @return \array[][]
     */
    public static function getDataVariantInvestments(): array
    {
        // http://127.0.0.1:7000/profile/invest
        return [
            'amount_available' => [
                'from' => 11,
                'to' => 101,
            ],
            'created_at' => [
                'from' => '08.03.2021',
                'to' => '09.03.2021',
            ],
            'my_investment' => 'exclude',
            'interest_rate_percent' => [
                'from' => 15,
                'to' => 18
            ],
            'loan_payment_status' => [
                'current',
                '1-15 days'
            ],
            'payment_status' => [
                'range1' => 'range1',
                'range2' => 'range2',
            ],
            'period' => [
                'from' => 1,
                'to' => 10,
            ],
            'loan' => [
                'type' => 'installments'
            ],
            'test' => [
                'sub' => [
                    'array' => 1
                ]
            ],
        ];
    }

    /**
     * @return \array[][]
     */
    public static function getDataVariantAdmin(): array
    {
        return [
            'testSet1' => [
                [
                    'amount_available' => [
                        'to' => 101,
                        'from' => 11,
                    ],
                    'createdAt' => [
                        'from' => '08.03.2021',
                        'to' => '09.03.2021',
                    ],
                    'interest_rate_percent' => [
                        'from' => 15,
                        'to' => 18
                    ],
                    'loan_payment_status' => [
                        'current',
                        '1-15 days'
                    ],
                    'payment_status' => [
                        'range1' => 'range1',
                        'range2' => 'range2',
                    ],
                    'period' => [
                        'from' => 1,
                        'to' => 10,
                    ],
                    'type' => 'installments',
                    'test' => [
                        'sub' => [
                            'array' => 1
                        ]
                    ],
                ]
            ]
        ];
    }
}
