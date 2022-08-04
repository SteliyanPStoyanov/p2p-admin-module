<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/daily.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'import_new_loans' => [
            'driver' => 'single',
            'path' => storage_path('logs/import/loans.log'),
        ],

        'import_installments' => [
            'driver' => 'single',
            'path' => storage_path('logs/import/installments.log'),
        ],

        'import_payments' => [
            'driver' => 'single',
            'path' => storage_path('logs/import/payments.log'),
        ],

        'import_repaid_loans' => [
            'driver' => 'single',
            'path' => storage_path('logs/import/repaid_loans.log'),
        ],

        'distr_installments' => [
            'driver' => 'single',
            'path' => storage_path('logs/distribute/installments.log'),
        ],

        'distr_loans' => [
            'driver' => 'single',
            'path' => storage_path('logs/distribute/loans.log'),
        ],

        'invest_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/invest/invest_service.log'),
        ],

        'invest_all' => [
            'driver' => 'single',
            'path' => storage_path('logs/invest/invest_all.log'),
        ],

        'invest_percent_diff' => [
            'driver' => 'single',
            'path' => storage_path('logs/invest/percent_diff.log'),
        ],

        'distr_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/distribute/distr_service.log'),
        ],

        'daily_archiver' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/archiver.log'),
        ],

        'app_checker' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/app_checker.log'),
        ],

        'daily_auto_rebuy' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/auto_rebuy.log'),
        ],

        'daily_repayment' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/repayment.log'),
        ],

        'daily_auto_invest' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/auto_invest.log'),
        ],

        'daily_maturity_refresh' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/maturity_refresh.log'),
        ],

        'daily_interest_refresh' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily/interest_refresh.log'),
        ],

        'daily_register_recall' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily_register/register_recall.log'),
        ],
        'daily_verify_recall' => [
            'driver' => 'single',
            'path' => storage_path('logs/daily_verify/verify_recall.log'),
        ],

        'settlement' => [
            'driver' => 'single',
            'path' => storage_path('logs/settlement/settlement.log'),

        ],

        'email_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/email/email_service.log'),
        ],

        'specific' => [
            'driver' => 'single',
            'path' => storage_path('logs/specific.log'),
        ],

        'health_check' => [
            'driver' => 'single',
            'path' => storage_path('logs/health_check.log'),
        ],

        'registration' => [
            'driver' => 'single',
            'path' => storage_path('logs/registration.log'),
        ],

        'final_payment_status' => [
            'driver' => 'single',
            'path' => storage_path('logs/final_payment_status.log'),
        ],

        'log_cleaner' => [
            'driver' => 'single',
            'path' => storage_path('logs/log_cleaner.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'log_monitor' => [
            'driver' => 'single',
            'path' => storage_path('logs/log_monitor.log'),
        ],

        'wallet_balance' => [
            'driver' => 'single',
            'path' => storage_path('logs/wallet_ballance.log'),
        ],

        'strategies_balance' => [
            'driver' => 'single',
            'path' => storage_path('logs/strategies_ballance.log'),
        ],

        'mongo' => [
            'driver' => 'single',
            'path' => storage_path('logs/mongo.log'),
        ],

        'loans_outstanding_amount' => [
            'driver' => 'single',
            'path' => storage_path('logs/loans_outstanding_amount.log'),
        ],

        'rollback_deposit' => [
            'driver' => 'single',
            'path' => storage_path('logs/rollback_deposit.log'),
        ],

        'loan_amount_available' => [
            'driver' => 'single',
            'path' => storage_path('logs/loan_amount_available.log'),
        ],

        'loan_investing_error' => [
            'driver' => 'single',
            'path' => storage_path('logs/loan_investing_error.log'),
        ],

        'importing_payments' => [
            'driver' => 'single',
            'path' => storage_path('logs/importing_payments.log'),
        ],

        'mass_invest_checker' => [
            'driver' => 'single',
            'path' => storage_path('logs/mass_invest/checker.log'),
        ],

        'mass_invest_fixer' => [
            'driver' => 'single',
            'path' => storage_path('logs/mass_invest/fixer.log'),
        ],

        'bonus_for_investor' => [
            'driver' => 'single',
            'path' => storage_path('logs/bonus_for_investor.log'),
        ],

        'bonus_tracking' => [
            'driver' => 'single',
            'path' => storage_path('logs/bonus_tracking.log'),
        ],

        'unit_test' => [
            'driver' => 'single',
            'path' => storage_path('logs/unit_test.log'),
        ],

        'affiliate' => [
            'driver' => 'single',
            'path' => storage_path('logs/affiliate.log'),
        ],
    ],
];
