# Stripe and Stripe Connect for your Laravel App

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegant/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/elegant/laravel-stripe)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elegant/laravel-stripe/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-stripe/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elegant/laravel-stripe/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-stripe/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elegant/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/elegant/laravel-stripe)

A simple way to attach Stripe Customer and Account to your Model in Laravel.

-   Stripe webhooks ready to use out of the box
-   Access Stripe php sdk easily

## Installation

You can install the package via composer:

```bash
composer require elegant/laravel-stripe
```

You should publish and run the migrations with:

```bash
php artisan vendor:publish --tag="stripe-migrations"
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="stripe-config"
```

This is the contents of the published config file:

```php
use Elegant\Stripe\Commands\CreateStripeWebhooksCommand;
use Elegant\Stripe\ModelRepository;

return [

    'models' => [
        'accounts' => [
            \App\Models\User::class,
        ],
        'customers' => [
            \App\Models\User::class,
        ],
        'repository' => ModelRepository::class,
    ],

    'cache' => [
        'accounts' => true,
        'customers' => false,
    ],

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'version' => env('STRIPE_VERSION', '2024-04-10'),

    /**
     * This is only used for the CreateStripeWebhooksCommand
     * You can add more webhooks directly from your Stripe Dashboard
     */
    'webhooks' => [
        [
            'url' => '/webhooks/stripe',
            'connect' => false,
            'enabled_events' => [
                ...CreateStripeWebhooksCommand::DEFAULT_WEBHOOKS_EVENTS,
            ],
        ],
    ],

];

```

## Prepare your model

### Setup your database

This package simply rely on columns you have to add to any Model having a stripe customer or account.
To do so, we provide a mirgation that will automatically add the required columns to your models.
To configure what models are related to stripe, you must edit the configs.

### Add the right trait

Add `HasStripeCustomer` trait to your Model:

```php
class Organization extends Model
{
    use HasStripeCustomer;
    // ...
}
```

## Configure Webhooks

### Configure Webhooks on Stripe

This package rely on the great `spatie/laravel-stripe-webhooks` package.

You must [follow their documentation](https://github.com/spatie/laravel-stripe-webhooks) to setup webhooks.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Quentin Gabriele](https://github.com/QuentinGab)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
