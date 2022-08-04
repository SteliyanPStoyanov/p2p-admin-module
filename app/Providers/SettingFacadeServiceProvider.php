<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Services\SettingFacade;

class SettingFacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('settingFacade', function() {
            return new SettingFacade();
        });
    }
}
