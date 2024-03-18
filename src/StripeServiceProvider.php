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
            ->hasConfigFile();
    }

    public function registeringPackage()
    {
        $this->app->bind(Stripe::class, function () {
            return new Stripe(
                config('stripe.key'),
                config('stripe.version'),
            );
        });

        $this->app->register(EventServiceProvider::class);
    }
}
