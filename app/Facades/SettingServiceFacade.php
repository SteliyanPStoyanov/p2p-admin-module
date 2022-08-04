<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SettingServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'settingFacade';
    }
}
