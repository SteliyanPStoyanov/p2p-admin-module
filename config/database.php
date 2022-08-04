<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'pgsql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'mongodb' => [
            'driver' => 'mongodb',
            'host' => env('MONGO_DB_HOST', '127.0.0.1'),
            'port' => env('MONGO_DB_PORT', 27017),
            'database' => env('MONGO_DB_DATABASE'),
            'username' => env('MONGO_DB_USERNAME'),
            'password' => env('MONGO_DB_PASSWORD'),
            'options' => [
            ],
        ],
        'sqlsrv_site' => [
            'driver' => env('DB_NEFIN_DRIVER'),
            'host' => env('DB_NEFIN_HOST'),
            'port' => env('DB_NEFIN_PORT'),
            'database' => env('DB_NEFIN_DBNAME'),
            'username' => env('DB_NEFIN_USERNAME'),
            'password' => env('DB_NEFIN_PASSWORD'),
        ],
        'sqlsrv_office' => [
            'driver' => env('DB_NEFIN_DRIVER'),
            'host' => env('DB_NEFIN_HOST'),
            'port' => env('DB_NEFIN_PORT'),
            'database' => env('DB_NEFIN_OFFICE_DBNAME'),
            'username' => env('DB_NEFIN_OFFICE_USERNAME'),
            'password' => env('DB_NEFIN_OFFICE_PASSWORD'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    | The reason why Redis does not use strings as DB names but indexes is that the goal and ability
    | of Redis databases is not to provide an outer level of dictionary: Redis dictionaries can't
    | scale to many dictionaries, but just to a small number (it is a tradeoff), nor we want to
    | provide nested data structures per design, so this are just "a few namespaces" and as a result
    | using a numerical small index seemed like the best option.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'predis'),
        'retry_after' => 35,

        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'session' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_SESSION_DB', '2'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'api' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_API_DB', '3'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'queues' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_QUEUES_DB', '4'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),

        ],

        'invests' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_INVESTS_DB', '5'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'emails' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_EMAILS_DB', '6'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'auto_invests' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_AUTO_INVESTS_DB', '7'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'relations' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_RELATIONS_DB', '8'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'investor_plans' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_PLANS_DB', '9'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],

        'loan_contracts' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_LOAN_CONTRACTS_DB', '10'),
            'read_write_timeout' => env('REDIS_WRITE_TIMEOUT', 60),
        ],
    ],
];
