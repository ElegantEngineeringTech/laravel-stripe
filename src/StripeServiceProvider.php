<?php

namespace Finller\Stripe;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StripeServiceProvider extends PackageServiceProvider
{
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
            ->hasMigration('add_stripe_ids_to_models_tables');
    }

    public function registeringPackage()
    {
        $this->app->bind(Stripe::class, function () {
            return new Stripe(
                config('stripe.secret'),
                config('stripe.version'),
            );
        });

        $this->app->register(EventServiceProvider::class);
    }
}
