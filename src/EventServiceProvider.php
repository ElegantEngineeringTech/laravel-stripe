<?php

namespace Elegantly\Stripe;

use Elegantly\Stripe\Listeners\AccountApplicationDeauthorized;
use Elegantly\Stripe\Listeners\AccountUpdated;
use Elegantly\Stripe\Listeners\CustomerDeleted;
use Elegantly\Stripe\Listeners\CustomerUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'stripe-webhooks::customer.updated' => [
            CustomerUpdated::class,
        ],
        'stripe-webhooks::customer.deleted' => [
            CustomerDeleted::class,
        ],
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
