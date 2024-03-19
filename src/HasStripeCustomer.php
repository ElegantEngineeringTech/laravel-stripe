<?php

use Finller\Stripe\Facades\Stripe;
use Finller\Stripe\StripeCustomerDoesntExistExecption;
use Illuminate\Support\Facades\Cache;

/**
 * @property ?string $stripe_customer_id
 */
trait HasStripeCustomer
{
    public function stripe(): \Stripe\StripeClient
    {
        return Stripe::client();
    }

    public function hasStripeCustomer(): bool
    {
        return (bool) $this->stripe_customer_id;
    }

    public function shouldCacheStripeCustomer(): bool
    {
        return (bool) config('stripe.cache.customers', false);
    }

    public function stripeCustomerCacheKey(): string
    {
        return get_class($this).':'.$this->getKey().':'.'stripe:customer';
    }

    public function cacheStripeCustomer(?array $params = [], $opts = null): static
    {
        $this->forgetStripeCustomer()->getStripeCustomer($params, $opts);

        return $this;
    }

    public function forgetStripeCustomer(): static
    {
        Cache::forget($this->stripeCustomerCacheKey());

        return $this;
    }

    public function createStripeCustomer(?array $params = [], $opts = null): \Stripe\Customer
    {
        if ($this->stripe_customer_id) {
            throw new Exception('['.get_class($this).':'.$this->getKey()."] Can't create, Stripe customer already exists ({$this->stripe_customer_id})");
        }

        $customer = $this->stripe()->customers->create([
            ...$params,
            'metadata' => [
                ...data_get($params, 'metadata', []),
                'model_type' => get_class($this),
                'model_id' => $this->getKey(),
            ],
        ], $opts);

        $this->syncWithStripeCustomer($customer);

        if ($this->shouldCacheStripeCustomer()) {
            Cache::forever(
                $this->stripeCustomerCacheKey(),
                $customer
            );
        }

        return $customer;
    }

    public function getStripeCustomer(?array $params = [], $opts = null): ?\Stripe\Customer
    {
        if (! $this->stripe_customer_id) {
            return null;
        }

        if ($this->shouldCacheStripeCustomer()) {
            return Cache::rememberForever(
                $this->stripeCustomerCacheKey(),
                fn () => $this->getFreshStripeCustomer($params, $opts)
            );
        }

        return $this->getFreshStripeCustomer($params, $opts);
    }

    public function getFreshStripeCustomer(?array $params = [], $opts = null): ?\Stripe\Customer
    {
        $customer = $this->stripe()->customers->retrieve($this->stripe_customer_id, $params, $opts);
        $this->syncWithStripeCustomer($customer);

        return $customer;
    }

    public function updateStripeCustomer(?array $params = null, $opts = null): \Stripe\Customer
    {
        if (! $this->stripe_customer_id) {
            throw StripeCustomerDoesntExistExecption::make($this, 'update acount');
        }

        $customer = $this->stripe()->customers->update($this->stripe_customer_id, $params, $opts);

        $this->syncWithStripeCustomer($customer);

        if ($this->shouldCacheStripeCustomer()) {
            Cache::forever(
                $this->stripeCustomerCacheKey(),
                $customer
            );
        }

        return $customer;
    }

    public function deleteStripeCustomer(?array $params = null, $opts = null): ?\Stripe\Customer
    {
        if (! $this->stripe_customer_id) {
            throw StripeCustomerDoesntExistExecption::make($this, 'delete acount');
        }

        $customer = $this->stripe()->customers->delete($this->stripe_customer_id, $params, $opts);

        $this->syncWithStripeCustomer(null);

        $this->forgetStripeCustomer();

        return $customer;
    }

    public function syncWithStripeCustomer(?\Stripe\Customer $customer): static
    {
        $this->stripe_customer_id = $customer?->id;

        $this->save();

        return $this;
    }
}
