<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'setting_key' => Setting::INTEREST_RATE_KEY,
                'name' => 'Interest rate',
                'description' => 'none',
                'default_value' => Setting::INTEREST_RATE_DEFAULT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::ASSIGNED_ORIGINATION_FEE_SHARE_KEY,
                'name' => 'Assigned origination fee share(%)',
                'description' => 'none',
                'default_value' => Setting::ASSIGNED_ORIGINATION_FEE_SHARE_DEFAULT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::MIN_INVESTMENT_AMOUNT_KEY,
                'name' => 'Minimum invest amount(in euro)',
                'description' => 'none',
                'default_value' => Setting::MIN_INVESTMENT_AMOUNT_DEFAULT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::MAX_ACCEPTABLE_OVERDUE_DAYS_KEY,
                'name' => 'Max acceptable overdue days',
                'description' => 'none',
                'default_value' => Setting::MAX_ACCEPTABLE_OVERDUE_DAYS_DEFAULT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::MAX_WRONG_LOGIN_ATTEMPTS_KEY,
                'name' => 'Max wrong login attempts',
                'description' => 'none',
                'default_value' => Setting::MAX_WRONG_LOGIN_ATTEMPTS_DEFAULT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::WRONG_LOGIN_BLOCK_DAYS_KEY,
                'name' => 'Wrong login block days',
                'description' => 'none',
                'default_value' => Setting::WRONG_LOGIN_BLOCK_DAYS_DEFAULT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::BONUS_DAYS_COUNT_FOR_CHECK_KEY,
                'name' => 'Bonus days count for check',
                'description' => 'none',
                'default_value' => Setting::BONUS_DAYS_COUNT_FOR_CHECK,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::BONUS_DAYS_COUNT_FOR_SEND_KEY,
                'name' => 'Bonus days count for send',
                'description' => 'none',
                'default_value' => Setting::BONUS_DAYS_COUNT_FOR_SEND,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::BONUS_MAX_AMOUNT_KEY,
                'name' => 'Bonus max amount',
                'description' => 'none',
                'default_value' => Setting::BONUS_MAX_AMOUNT,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::BONUS_MIN_INVESTED_AMOUNT_KEY,
                'name' => 'Bonus min invested amount',
                'description' => 'none',
                'default_value' => Setting::BONUS_MIN_INVESTED_AMOUNT,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::BONUS_PERCENT_KEY,
                'name' => 'Bonus percent',
                'description' => 'none',
                'default_value' => Setting::BONUS_PERCENT,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::AUTO_INVEST_MIN_PORTFOLIO_SIZE,
                'name' => 'Auto invest min portfolio size',
                'description' => 'none',
                'default_value' => Setting::AUTO_INVEST_MIN_PORTFOLIO_SIZE_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::AUTO_INVEST_MIN_AMOUNT,
                'name' => 'Auto invest min amount',
                'description' => 'none',
                'default_value' => Setting::AUTO_INVEST_MIN_AMOUNT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::AUTO_INVEST_DEFAULT_AMOUNT,
                'name' => 'Auto invest default amount',
                'description' => 'none',
                'default_value' => Setting::AUTO_INVEST_DEFAULT_AMOUNT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::DELAY_BUYING_AFTER_DEPOSIT_LOADED_KEY,
                'name' => 'Delay for starting buying, after deposit loaded, minutes',
                'description' => 'none',
                'default_value' => Setting::DELAY_BUYING_AFTER_DEPOSIT_LOADED_DEFAUL_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::MIN_PAYMENTS_AMOUNT_FOR_ALERT_KEY,
                'name' => 'Сума в лева, срещу която пускаме Payment Alert на daily база, ако нямаме достатъчно плащания.',
                'description' => 'none',
                'default_value' => Setting::MIN_PAYMENTS_AMOUNT_FOR_ALERT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'setting_key' => Setting::PREMIUM_LIMIT_VALUE_KEY,
                'name' => 'Premium should be between -15% - +15%',
                'description' => 'none',
                'default_value' => Setting::PREMIUM_LIMIT_VALUE,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
        ];

        DB::table('setting')->insert($settings);
    }
}
