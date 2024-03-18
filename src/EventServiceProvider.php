<?php

namespace Finller\Stripe;

use Finller\Stripe\Listeners\AccountApplicationDeauthorized;
use Finller\Stripe\Listeners\AccountUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'stripe-webhooks::account.updated' => [
            AccountUpdated::class,
        ],
        'stripe-webhooks::account.application.deauthorized' => [
            AccountApplicationDeauthorized::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
