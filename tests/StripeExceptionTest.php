<?php

declare(strict_types=1);

use Elegantly\Stripe\Exceptions\StripeAccountAlreadyExistsException;
use Elegantly\Stripe\Exceptions\StripeAccountDoesntExistException;
use Elegantly\Stripe\Exceptions\StripeCustomerAlreadyExistsException;
use Elegantly\Stripe\Exceptions\StripeCustomerDoesntExistException;
use Elegantly\Stripe\Facades\Stripe;
use Elegantly\Stripe\HasStripeAccount;
use Elegantly\Stripe\HasStripeCustomer;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    config()->set('stripe.secret', 'sk_test_placeholder');
});

it('rejects duplicate Stripe resources before contacting Stripe', function (string $method, string $attribute, string $id, string $exception) {
    $model = new class extends Model
    {
        use HasStripeAccount, HasStripeCustomer {
            HasStripeAccount::stripe insteadof HasStripeCustomer;
        }
    };
    $model->id = 42;
    $model->setAttribute($attribute, $id);

    Stripe::shouldReceive('client')->never();

    expect(fn () => $model->{$method}())->toThrow($exception, $id);
})->with([
    'account' => ['createStripeAccount', 'stripe_account_id', 'acct_existing', StripeAccountAlreadyExistsException::class],
    'v2 account' => ['createStripeV2Account', 'stripe_account_id', 'acct_existing', StripeAccountAlreadyExistsException::class],
    'customer' => ['createStripeCustomer', 'stripe_customer_id', 'cus_existing', StripeCustomerAlreadyExistsException::class],
]);

it('rejects operations on missing Stripe resources before contacting Stripe', function (string $method, array $arguments, string $exception, string $operation) {
    $model = new class extends Model
    {
        use HasStripeAccount, HasStripeCustomer {
            HasStripeAccount::stripe insteadof HasStripeCustomer;
        }
    };
    $model->id = 42;

    Stripe::shouldReceive('client')->never();

    expect(fn () => $model->{$method}(...$arguments))
        ->toThrow($exception, '['.get_class($model).":42] Can't {$operation}, Stripe ");
})->with([
    'update account' => ['updateStripeAccount', [], StripeAccountDoesntExistException::class, 'update account'],
    'update tax settings' => ['updateStripeAccountTaxSettings', [], StripeAccountDoesntExistException::class, 'update account tax settings'],
    'update capability' => ['updateStripeAccountCapability', ['transfers'], StripeAccountDoesntExistException::class, 'update account capability'],
    'delete account' => ['deleteStripeAccount', [], StripeAccountDoesntExistException::class, 'delete account'],
    'login link' => ['createStripeLoginLink', [], StripeAccountDoesntExistException::class, 'create login link'],
    'account link' => ['createStripeAccountLink', [], StripeAccountDoesntExistException::class, 'create account link'],
    'account session' => ['createStripeAccountSession', [], StripeAccountDoesntExistException::class, 'create account session'],
    'payout' => ['payoutStripeAccount', [], StripeAccountDoesntExistException::class, 'payout'],
    'transfer' => ['createStripeAccountTransfer', [], StripeAccountDoesntExistException::class, 'transfer'],
    'update customer' => ['updateStripeCustomer', [], StripeCustomerDoesntExistException::class, 'update customer'],
    'delete customer' => ['deleteStripeCustomer', [], StripeCustomerDoesntExistException::class, 'delete customer'],
    'checkout' => ['checkoutStripeCustomer', [], StripeCustomerDoesntExistException::class, 'checkout'],
    'billing portal' => ['createStripeBillingPortalSesssion', [], StripeCustomerDoesntExistException::class, 'create billing portal'],
]);
