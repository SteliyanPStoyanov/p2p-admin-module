<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // we continue reducing active loans, by importing manually unlisted loans and the unlist them
        $schedule->command('script:unlisted-loans:import')->dailyAt('02:10');
        // $schedule->command('script:unlisted-loans:import')->everyTwoMinutes()->between('00:08', '00:14');
        $schedule->command('script:loans:auto-rebuy')->dailyAt('02:15');

        // we import and distribute repaid loans, we reduce count of active loans
        $schedule->command('script:repaid-loans:import')->dailyAt('02:20');
        // $schedule->command('script:repaid-loans:distribute')->everyTwoMinutes()->between('00:30', '00:38');
        $schedule->command('script:repaid-loans:distribute')->dailyAt('02:30');

        // we import and distribute repaid installments
        // $schedule->command('script:repaid-installments:import')->everyTwoMinutes()->between('00:40', '00:44');
        $schedule->command('script:repaid-installments:import')->dailyAt('02:40');
        // $schedule->command('script:repaid-installments:distribute')->everyTwoMinutes()->between('00:46', '00:52');
        $schedule->command('script:repaid-installments:distribute')->dailyAt('02:50');

        // daily updates on: payment_statuses, quality ranges, maturity ranges, accrued/late interest on investor installments
        $schedule->command('script:daily-maturity-refresh')->dailyAt('03:30');
        $schedule->command('script:daily-interest-refresh')->dailyAt('03:40');

        // import new loans & installments for sell
        // $schedule->command('script:loans:import')->everyTwoMinutes()->between('01:46', '01:52');
        $schedule->command('script:loans:import')->dailyAt('03:50');
        // $schedule->command('script:installments:import')->everyMinute()->between('02:00', '02:10');
        $schedule->command('script:installments:import')->dailyAt('04:00');

        // archived unused data
        $schedule->command('script:daily-archive')->dailyAt('04:10');

        // we should update payment statuses after importing to handle new imported loans
        $schedule->command('script:daily-payment-status-refresh')->dailyAt('04:20');

        // auto-invest
        $schedule->command('script:auto-invest')->dailyAt('04:20');

        if (isProd()) {
            // send settlement
            $schedule->command('script:daily-settlement')->dailyAt('07:00');
        }

        // send wallet balance
        $schedule->command('script:wallet-balance')->dailyAt('07:07');

        // check logs for errors
        $schedule->command('script:logs:monitor')->dailyAt('07:15');

        // check outstanding amounts
        $schedule->command('script:loans:bad-invested-amount')->dailyAt('07:17');

        if (isProd()) {
            // registration recall
            $schedule->command('script:daily-register-recall')->dailyAt('12:55');

            // verification recall
            $schedule->command('script:daily-verify-recall')->dailyAt('13:05');

            // send month period settlement
            $schedule->command('script:period-settlement monthly')->monthlyOn(1, '07:30');

            // send week period settlement
            $schedule->command('script:period-settlement weekly')->weeklyOn(4,'07:20');
        }

        // run auto-invest strategies when deposit is created(15 min later)
        $schedule->command('script:auto-invest-deposit-added')->everyMinute();

        // bonuses
        $schedule->command('script:bonus:prepare')->dailyAt('07:10');
        $schedule->command('script:bonus:handle')->dailyAt('07:12');
        $schedule->command('script:bonus-tracking')->dailyAt('07:20');


        // alert for lost invest all records
        $schedule->command('script:mass-invest:fixer')->dailyAt('06:00');
        $schedule->command('script:mass-invest:checker')->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
