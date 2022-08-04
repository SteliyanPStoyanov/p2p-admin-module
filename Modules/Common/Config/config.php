<?php

use Modules\Common\Entities\Currency;

return [
    'name' => 'Common',
    'currencyId' => Currency::ID_EUR,
    'currencyRate' => env('CURRENCY_RATE', Currency::CURRENCY_RATE),
    'investorEmailChunk' => 50,
    'currencySimbol' => [
        Currency::ID_EUR => '€'
    ]
];
