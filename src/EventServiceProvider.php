<?php

namespace Elegant\Stripe;

use Elegant\Stripe\Listeners\AccountApplicationDeauthorized;
use Elegant\Stripe\Listeners\AccountUpdated;
use Elegant\Stripe\Listeners\CustomerDeleted;
use Elegant\Stripe\Listeners\CustomerUpdated;
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
