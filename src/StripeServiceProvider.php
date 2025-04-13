<?php

declare(strict_types=1);

namespace Elegantly\Stripe;

use Elegantly\Stripe\Commands\CreateStripeWebhooksCommand;
use Elegantly\Stripe\Listeners\AccountApplicationDeauthorized;
use Elegantly\Stripe\Listeners\AccountUpdated;
use Elegantly\Stripe\Listeners\CustomerDeleted;
use Elegantly\Stripe\Listeners\CustomerUpdated;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StripeServiceProvider extends PackageServiceProvider
{
    /**
     * @var array<string, array<int, string>>
     */
    protected array $listen = [
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

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-stripe')
            ->hasConfigFile()
            ->hasCommand(CreateStripeWebhooksCommand::class)
            ->hasMigration('add_stripe_ids_to_models_tables');
    }

    public function registeringPackage(): void
    {
        $this->app->bind(Stripe::class, function () {
            return new Stripe(
                config('stripe.secret'),
                config('stripe.version'),
            );
        });

        $this->registerListeners();
    }

    protected function registerListeners(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
