<?php

namespace Modules\Admin\Entities;

use Modules\Common\Observers\SettingObserver;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Setting extends BaseModel implements LoggerInterface
{
    public const ASSIGNED_ORIGINATION_FEE_SHARE_KEY = 'assigned_origination_fee_share';
    public const INTEREST_RATE_KEY = 'interest_rate';
    public const MIN_INVESTMENT_AMOUNT_KEY = 'min_invest_amount';
    public const MAX_ACCEPTABLE_OVERDUE_DAYS_KEY = 'max_acceptable_overdue_days';
    public const MAX_WRONG_LOGIN_ATTEMPTS_KEY = 'max_wrong_login_attempts';
    public const WRONG_LOGIN_BLOCK_DAYS_KEY = 'wrong_login_block_days';
    public const SHOW_FORMAT = 'd.m.Y';
    public const DB_DATE_FORMAT = 'Y-m-d';

    public const BONUS_DAYS_COUNT_FOR_SEND_KEY = 'bonus_days_count_for_send';
    public const BONUS_DAYS_COUNT_FOR_CHECK_KEY = 'bonus_days_count_for_check';
    public const BONUS_MAX_AMOUNT_KEY = 'bonus_max_amount';
    public const BONUS_MIN_INVESTED_AMOUNT_KEY = 'bonus_min_invested_amount';
    public const BONUS_PERCENT_KEY = 'bonus_percent';

    public const AUTO_INVEST_MIN_PORTFOLIO_SIZE = 'auto_invest_min_portfolio_size';
    public const AUTO_INVEST_MIN_AMOUNT = 'auto_invest_min_amount';
    public const AUTO_INVEST_DEFAULT_AMOUNT = 'auto_invest_default_amount';

    public const DELAY_BUYING_AFTER_DEPOSIT_LOADED_KEY = 'delay_buying_after_deposit_loaded';

    public const MIN_PAYMENTS_AMOUNT_FOR_ALERT_KEY = 'min_payments_amount_for_alert';
    public const PREMIUM_LIMIT_VALUE_KEY = 'premium_limit_value';

    public const BONUS_DAYS_COUNT_FOR_CHECK = 30;
    public const BONUS_DAYS_COUNT_FOR_SEND = 32;
    public const BONUS_MAX_AMOUNT = 500;
    public const BONUS_MIN_INVESTED_AMOUNT = 500;
    public const BONUS_PERCENT = 1;

    public const INTEREST_RATE_DEFAULT_VALUE = 16;
    public const ASSIGNED_ORIGINATION_FEE_SHARE_DEFAULT_VALUE = 10;
    public const MIN_INVESTMENT_AMOUNT_DEFAULT_VALUE = 10;
    public const MAX_ACCEPTABLE_OVERDUE_DAYS_DEFAULT_VALUE = 60;
    public const MAX_WRONG_LOGIN_ATTEMPTS_DEFAULT_VALUE = 10;
    public const WRONG_LOGIN_BLOCK_DAYS_DEFAULT_VALUE = 1;

    public const AUTO_INVEST_MIN_PORTFOLIO_SIZE_VALUE = 10;
    public const AUTO_INVEST_MIN_AMOUNT_VALUE = 10;
    public const AUTO_INVEST_DEFAULT_AMOUNT_VALUE = 30;

    public const DELAY_BUYING_AFTER_DEPOSIT_LOADED_DEFAUL_VALUE = 15;

    public const MIN_PAYMENTS_AMOUNT_FOR_ALERT_VALUE = 5000;
    public const PREMIUM_LIMIT_VALUE = 15;


    public const SETTING_KEYS = [
        self::ASSIGNED_ORIGINATION_FEE_SHARE_KEY,
        self::INTEREST_RATE_KEY,
        self::MIN_INVESTMENT_AMOUNT_KEY,
        self::MAX_ACCEPTABLE_OVERDUE_DAYS_KEY,
        self::BONUS_DAYS_COUNT_FOR_SEND_KEY,
        self::BONUS_DAYS_COUNT_FOR_CHECK_KEY,
        self::BONUS_MAX_AMOUNT_KEY,
        self::BONUS_MIN_INVESTED_AMOUNT_KEY,
        self::BONUS_PERCENT_KEY,
        self::MAX_WRONG_LOGIN_ATTEMPTS_KEY,
        self::WRONG_LOGIN_BLOCK_DAYS_KEY,
        self::AUTO_INVEST_DEFAULT_AMOUNT,
        self::AUTO_INVEST_MIN_AMOUNT,
        self::AUTO_INVEST_MIN_PORTFOLIO_SIZE,
        self::DELAY_BUYING_AFTER_DEPOSIT_LOADED_KEY,
        self::PREMIUM_LIMIT_VALUE_KEY,
    ];

    protected $table = 'setting';
    protected $primaryKey = 'setting_key';
    protected $keyType = 'string';

    protected $fillable = [
        'setting_key',
        'name',
        'description',
        'default_value',
    ];

    protected $with = ['creator', 'updater'];

    public $incrementing = false;

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(SettingObserver::class);
    }

    public function getSettingId()
    {
        return $this->setting_key;
    }
}
