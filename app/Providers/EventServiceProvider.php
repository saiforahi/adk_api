<?php

namespace App\Providers;

use App\Events\v1\UploadImageEvent;
use App\Events\v1\CommissionDistributionEvent;
use App\Events\v1\DealerCommissionDistributionEvent;
use App\Listeners\v1\UploadImageEventListener;
use App\Listeners\v1\CommissionDistributionEventListener;
use App\Listeners\v1\DealerCommissionDistributionListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UploadImageEvent::class => [
            UploadImageEventListener::class,
        ],
        CommissionDistributionEvent::class => [
            CommissionDistributionEventListener::class,
        ],
        DealerCommissionDistributionEvent::class => [
            DealerCommissionDistributionListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
