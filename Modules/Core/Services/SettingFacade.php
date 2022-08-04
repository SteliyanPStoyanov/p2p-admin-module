<?php

namespace Modules\Core\Services;

use Modules\Admin\Entities\Setting;
use Modules\Core\Exceptions\ProblemException;

class SettingFacade
{
    public function getInvestDelayAfterDeposit(): int
    {
        $value = self::getSettingValue(Setting::DELAY_BUYING_AFTER_DEPOSIT_LOADED_KEY);
        if (!empty($value)) {
            return intval($value);
        }

        return Setting::DELAY_BUYING_AFTER_DEPOSIT_LOADED_DEFAUL_VALUE;
    }

    public function getMinAmountForInvest(): float
    {
        $value = self::getSettingValue(Setting::MIN_INVESTMENT_AMOUNT_KEY);
        if (!empty($value)) {
            return floatval($value);
        }

        return Setting::MIN_INVESTMENT_AMOUNT_DEFAULT_VALUE;
    }

    public function getOriginatorFeePercent(): float
    {
        $value = self::getSettingValue(Setting::ASSIGNED_ORIGINATION_FEE_SHARE_KEY);
        if (!empty($value)) {
            return floatval($value);
        }

        return Setting::ASSIGNED_ORIGINATION_FEE_SHARE_DEFAULT_VALUE;
    }

    /**
     * @param string $settingKey
     *
     * @return mixed
     *
     * @throws ProblemException
     */
    public function getSettingValue(string $settingKey)
    {
        if (!in_array($settingKey, Setting::SETTING_KEYS)) {
            throw new ProblemException(
                'Setting with key  ' . $settingKey . ' do not exists!'
            );
        }

        $setting =  (new CacheService())->get($settingKey);
        if ($setting === null) {
            $setting = $this->cacheSetting($settingKey);
        }

        return $setting->default_value;
    }

    /**
     * @param string $settingKey
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Setting|object|null
     * @throws ProblemException
     */
    protected function cacheSetting(string $settingKey)
    {
        $setting = Setting::where(
            [
                ['setting_key', '=', $settingKey],
                ['active', '=', 1],
            ]
        )->first();

        if (empty($setting->default_value)) {
            throw new ProblemException(
                'Please update value of setting  ' . $settingKey
            );
        }

        (new CacheService())->set(
            $setting->setting_key,
            $setting,
            config('cache.cacheSettingsTimeOut')
        );

        return $setting;
    }
}
