# Stripe and Stripe Connect Integration for Your Laravel Application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegantly/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-stripe)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-stripe/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-stripe/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-stripe/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-stripe/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elegantly/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-stripe)

A simple way to attach Stripe Customer and Account to your Model in Laravel.

-   Stripe webhooks ready to use out of the box
-   Access Stripe PHP SDK easily

## Installation Guide

You can install the package via Composer:

```bash
composer require elegantly/laravel-stripe
```

You can publish the configuration file with:

```bash
php artisan vendor:publish --tag="stripe-config"
```

This is the content of the published configuration file:

```php
use Elegantly\Stripe\Commands\CreateStripeWebhooksCommand;
use Elegantly\Stripe\ModelRepository;

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

    'version' => env('STRIPE_VERSION'),

    /**
     * This is only used for the CreateStripeWebhooksCommand.
     * You can add more webhooks directly from your Stripe Dashboard.
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

## Usage Examples

Creating and retrieving a Stripe Account:

```php
$user->createStripeAccount();
$user->getStripeAccount();
```

Creating and retrieving a Stripe Customer:

```php
$user->createStripeCustomer();
$user->getStripeCustomer();
```

## Model Preparation

### Database Setup

This package relies on columns you need to add to any Model that has a Stripe customer or account. To do so, we provide a migration that will automatically add the required columns to your models. To configure which models are related to Stripe, you must edit the configuration file.

### Adding the Necessary Trait

Add the `HasStripeCustomer` trait to your Model:

```php
class Organization extends Model
{
    use HasStripeCustomer;
    // ...
}
```

### Configuring Models

By default, the package assumes that your Stripe objects are attached to your User model. If this is not the case, you will need to edit the configuration file like this:

```php
'models' => [
    'accounts' => [
        \App\Models\User::class,
    ],
    'customers' => [
        \App\Models\Organization::class,
    ],
    'repository' => ModelRepository::class,
],
```

### Running Migrations

```bash
php artisan vendor:publish --tag="stripe-migrations"
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-migrations"
php artisan migrate
```

## Webhook Configuration

This package comes with the command `stripe:create-webhooks`, which will create and configure webhooks on the Stripe dashboard for you. All you need to do is edit the webhooks and the endpoints you want to enable in the configuration file.

### Editing Configuration

For example, you could configure two different webhooks with different routes and endpoints like so:

```php
return [

    // Other configurations...

    /**
     * This is only used for the CreateStripeWebhooksCommand.
     * You can add more webhooks directly from your Stripe Dashboard.
     */
    'webhooks' => [
        [
            'url' => '/stripe/webhook/account',
            'connect' => false,
            'enabled_events' => [
                ...CreateStripeWebhooksCommand::DEFAULT_WEBHOOKS_EVENTS,
                'checkout.session.expired',
                'checkout.session.completed',
                'checkout.session.async_payment_succeeded',
                'checkout.session.async_payment_failed',
            ],
        ],
        [
            'url' => '/stripe/webhook/connect',
            'connect' => true,
            'enabled_events' => [
                ...CreateStripeWebhooksCommand::DEFAULT_WEBHOOKS_EVENTS,
            ],
        ],
    ],

];
```

### Running the Command

Once you are satisfied with the configurations, you just need to run:

```bash
php artisan stripe:create-webhooks
```

### Activating Webhooks on Stripe

All the webhooks configured by this command are disabled by default to prevent unexpected behavior. When you are ready, activate them from your Stripe Dashboard.

### Listening to Stripe Events in Your Application

Now that Stripe sends webhooks to your app, you can listen to them from `EventServiceProvider`.

This package relies on the great `spatie/laravel-stripe-webhooks` package. You must [follow their documentation](https://github.com/spatie/laravel-stripe-webhooks) to set up your listeners.

### Upgrading Stripe's Webhook version

## Running Tests

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
