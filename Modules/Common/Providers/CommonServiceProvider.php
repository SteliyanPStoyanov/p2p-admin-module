<?php

namespace Modules\Common\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\Common\Console\AddContractNumbers;
use Modules\Common\Console\AppChecker;
use Modules\Common\Console\AutoInvest;
use Modules\Common\Console\AutoInvestOnDeposit;
use Modules\Common\Console\BonusHandle;
use Modules\Common\Console\BonusPrepare;
use Modules\Common\Console\BonusTracking;
use Modules\Common\Console\CalculateInvestorBalance;
use Modules\Common\Console\DailyArchiver;
use Modules\Common\Console\DailyAutoRebuy;
use Modules\Common\Console\DailyLoanInterestRefresh;
use Modules\Common\Console\DailyLoanMaturityRefresh;
use Modules\Common\Console\DailyPaymentsCheck;
use Modules\Common\Console\DailyPaymentStatusRefresh;
use Modules\Common\Console\DailyRegisterRecall;
use Modules\Common\Console\DailySettlement;
use Modules\Common\Console\DailyVerifyRecall;
use Modules\Common\Console\DistributeInstallments;
use Modules\Common\Console\DistributeLoans;
use Modules\Common\Console\FixFirstInstallmentInterest;
use Modules\Common\Console\ImportInstallments;
use Modules\Common\Console\ImportLoans;
use Modules\Common\Console\ImportRepaidInstallments;
use Modules\Common\Console\ImportRepaidLoans;
use Modules\Common\Console\ImportUnlistedLoans;
use Modules\Common\Console\LoanOutstandingAmountChecker;
use Modules\Common\Console\LogCleaner;
use Modules\Common\Console\LogMonitor;
use Modules\Common\Console\ManualParseImportedPayment;
use Modules\Common\Console\MassInvestChecker;
use Modules\Common\Console\MassInvestFixer;
use Modules\Common\Console\PeriodSettlement;
use Modules\Common\Console\RecalculateInvestorBalance;
use Modules\Common\Console\RevertInvestorTransactions;
use Modules\Common\Console\RollbackDeposit;
use Modules\Common\Console\StrategiesBalance;
use Modules\Common\Console\UnblockLoans;
use Modules\Common\Console\UpdateLoanFinalPaymentStatus;
use Modules\Common\Console\WalletBalance;

class CommonServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Common';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'common';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
                module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes(
            [
                $sourcePath => $viewPath
            ],
            ['views', $this->moduleNameLower . '-module-views']
        );

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    public function registerCommands()
    {
        $this->commands([
                AddContractNumbers::class,
                AppChecker::class,
                AutoInvest::class,
                AutoInvestOnDeposit::class,
                BonusHandle::class,
                BonusPrepare::class,
                BonusTracking::class,
                CalculateInvestorBalance::class,
                DailyArchiver::class,
                DailyAutoRebuy::class,
                DailyLoanInterestRefresh::class,
                DailyLoanMaturityRefresh::class,
                DailyPaymentsCheck::class,
                DailyPaymentStatusRefresh::class,
                DailyRegisterRecall::class,
                DailySettlement::class,
                DailyVerifyRecall::class,
                DistributeInstallments::class,
                DistributeLoans::class,
                FixFirstInstallmentInterest::class,
                ImportInstallments::class,
                ImportLoans::class,
                ImportRepaidInstallments::class,
                ImportRepaidLoans::class,
                ImportUnlistedLoans::class,
                LoanOutstandingAmountChecker::class,
                LogCleaner::class,
                LogMonitor::class,
                ManualParseImportedPayment::class,
                MassInvestChecker::class,
                MassInvestFixer::class,
                PeriodSettlement::class,
                RevertInvestorTransactions::class,
                RollbackDeposit::class,
                StrategiesBalance::class,
                UnblockLoans::class,
                UpdateLoanFinalPaymentStatus::class,
                WalletBalance::class,
        ]);
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
