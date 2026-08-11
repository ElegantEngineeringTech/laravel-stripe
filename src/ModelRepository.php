<?php

declare(strict_types=1);

namespace Elegantly\Stripe;

use Illuminate\Database\Eloquent\Model;
use Stripe\Account;
use Stripe\Customer;

class ModelRepository
{
    public static function findAccount(string $stripeAccountId): ?Model
    {
        return static::findFromModels(
            models: config('stripe.models.accounts'),
            stripeId: $stripeAccountId
        );
    }

    public static function findCustomer(string $stripeCustomerId): ?Model
    {
        return static::findFromModels(
            models: config('stripe.models.customers'),
            stripeId: $stripeCustomerId
        );
    }

    public static function findAccountFromStripeObject(Account $account): ?Model
    {
        return static::findAccount($account->id);
    }

    public static function findCustomerFromStripeObject(Customer $customer): ?Model
    {
        return static::findCustomer($customer->id);
    }

    protected static function findFromStripeObject(Account|Customer $object): ?Model
    {
        $model_type = data_get($object->metadata, 'model_type');
        $model_id = data_get($object->metadata, 'model_id');

        if (! $model_type || ! $model_id) {
            return null;
        }

        return $model_type::find($model_id);
    }

    /**
     * @param  array<class-string<Model>, string>  $models
     */
    protected static function findFromModels(
        array $models,
        string $stripeId
    ): ?Model {
        foreach ($models as $model => $column) {
            $instance = $model::query()
                ->where($column, $stripeId)
                ->first();

            if ($instance) {
                return $instance;
            }
        }

        return null;
    }
}
