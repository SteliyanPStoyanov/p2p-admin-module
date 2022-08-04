<?php

namespace App\Providers;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Admin\Listeners\LockoutEventListener;
use Modules\Common\Events\AffiliateEvents;
use Modules\Common\Events\LoanAmountAvailableEvents;
use Modules\Common\Events\RestorePassword;
use Modules\Common\Listeners\AffiliateListener;
use Modules\Common\Listeners\LoanAmountAvailableListener;
use Modules\Common\Listeners\RestorePasswordListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RestorePassword::class =>[
            RestorePasswordListener::class,
        ],
        Lockout::class => [
            LockoutEventListener::class,
        ],
        LoanAmountAvailableEvents::class =>[
            LoanAmountAvailableListener::class,
        ],
        AffiliateEvents::class =>[
            AffiliateListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
