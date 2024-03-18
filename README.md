# Stripe and Stripe Connect for your Laravel App

[![Latest Version on Packagist](https://img.shields.io/packagist/v/finller/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/finller/laravel-stripe)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/finller/laravel-stripe/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/finller/laravel-stripe/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/finller/laravel-stripe/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/finller/laravel-stripe/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/finller/laravel-stripe.svg?style=flat-square)](https://packagist.org/packages/finller/laravel-stripe)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require finller/laravel-stripe
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-stripe-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-stripe-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-stripe-views"
```

## Prepare your model

First of all you have to add the column `stripe_account_id` to your Model (User or any other one).

Here is an exemple of a migration for the table `users`:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_account_id');
        });
    }
};
```

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
