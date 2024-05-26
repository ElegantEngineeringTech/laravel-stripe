<?php

namespace Elegantly\Stripe;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Stripe\Account;
use Stripe\Customer;

class ModelRepository
{
    public static function findAccount(string $stripeAccountId): ?Model
    {
        return static::findFromModels(
            models: Arr::wrap(config('stripe.models.accounts')),
            columnName: 'stripe_account_id',
            stripeId: $stripeAccountId
        );
    }

    public static function findCustomer(string $stripeCustomerId): ?Model
    {
        return static::findFromModels(
            models: Arr::wrap(config('stripe.models.customers')),
            columnName: 'stripe_customer_id',
            stripeId: $stripeCustomerId
        );
    }

    public static function findAccountFromStripeObject(Account $account): ?Model
    {
        $model = static::findFromStripeObject($account);

        if (!$model) {
            return static::findAccount($account->id);
        }

        $model_type = get_class($model);
        $model_id = $model->getKey();

        /** @var ?string $model_stripe_account_id */
        $model_stripe_account_id = $model->stripe_account_id; // @phpstan-ignore-line

        if ($model_stripe_account_id !== $account->id) {
            throw new Exception("[{$model_type}:{$model_id}] Conflict between Stripe account ID and Stripe account metadata: {$model_stripe_account_id} !== {$account->id}", 500);
        }

        return $model;
    }

    public static function findCustomerFromStripeObject(Customer $customer): ?Model
    {
        $model = static::findFromStripeObject($customer);

        if (!$model) {
            return static::findCustomer($customer->id);
        }

        $model_type = get_class($model);
        $model_id = $model->getKey();

        /** @var ?string $model_stripe_customer_id */
        $model_stripe_customer_id = $model->stripe_customer_id; // @phpstan-ignore-line

        if ($model_stripe_customer_id !== $customer->id) {
            throw new Exception("[{$model_type}:{$model_id}] Conflict between Stripe customer ID and Stripe customer metadata: {$model_stripe_customer_id} !== {$customer->id}", 500);
        }

        return $model;
    }

    protected static function findFromStripeObject(Account|Customer $object): ?Model
    {
        $model_type = data_get($object->metadata, 'model_type');
        $model_id = data_get($object->metadata, 'model_id');

        if (!$model_type || !$model_id) {
            return null;
        }

        return $model_type::find($model_id);
    }

    protected static function findFromModels(
        array $models,
        string $columnName,
        string $stripeId
    ): ?Model {
        foreach ($models as $model) {
            $instance = $model::query()
                ->where($columnName, $stripeId)
                ->first();

            if ($instance) {
                return $instance;
            }
        }

        return null;
    }
}
