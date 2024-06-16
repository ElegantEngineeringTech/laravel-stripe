# Stripe and Stripe Connect for your Laravel App

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elegantly/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-stripe)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-stripe/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-stripe/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ElegantEngineeringTech/laravel-stripe/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ElegantEngineeringTech/laravel-stripe/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elegantly/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/elegantly/laravel-stripe)

A simple way to attach Stripe Customer and Account to your Model in Laravel.

-   Stripe webhooks ready to use out of the box
-   Access Stripe php sdk easily

## Installation

You can install the package via composer:

```bash
composer require elegantly/laravel-stripe
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

## Example

Creating and retreiving Stripe Account:

```php
$user->createStripeAccount();
$user->getStripeAccount();
```

Creating and retreiving Stripe Customer:

```php
$user->createStripeCustomer();
$user->getStripeCustomer();
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

## Configuring Webhooks

This package comes the command `stripe:create-webhooks`, it will create and configure webhooks on Stripe dashboard for you.
All you need to do is edit the webhooks and the endpoints you want to enable in the config file.

### Edit your config

For example you could configure two different webhooks with different routes and endpoints like so:

```php
return [

    // configs ...

    /**
     * This is only used for the CreateStripeWebhooksCommand
     * You can add more webhooks directly from your Stripe Dashboard
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

### Run the command

Now that you are happy with the configs , you just have to run:

```bash
php artisan stripe:create-webhooks
```

### Activate the webhooks from Stripe

All the webhooks configured by this command are disabled by default to prevent unexpected behaviour. When you are ready, just activate them from your Stripe Dashboard.

### Listen to Stripe events in your App

Now that Stripe actually send webhooks to your app, you can listen to them from `EventServiceProvider`.

This package rely on the great `spatie/laravel-stripe-webhooks` package.

You must [follow their documentation](https://github.com/spatie/laravel-stripe-webhooks) to setup your listeners.

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
