<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Common\Traits\PinTrait;

class ValidatorServiceProvider extends ServiceProvider
{
    use PinTrait;

    public function boot()
    {
        $this->app['validator']->extend(
            'is_valid_pin',
            function ($attribute, $value, $parameters) {
                return $this->isValidPin($value);
            }
        );
    }
}
