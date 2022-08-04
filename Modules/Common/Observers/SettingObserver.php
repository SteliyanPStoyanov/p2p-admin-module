<?php

namespace Modules\Common\Observers;

use Modules\Admin\Entities\Setting;
use Modules\Core\Services\CacheService;

class SettingObserver
{
    /**
     * @param Setting $setting
     */
     public function updating(Setting $setting)
    {
        (new CacheService())->remove($setting->setting_key);

    }
}
