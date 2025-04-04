<?php

declare(strict_types=1);

namespace Elegantly\Stripe;

use Elegantly\Stripe\Facades\Stripe;
use Illuminate\Support\Facades\Cache;

/**
 * @property ?string $stripe_account_id
 */
trait HasStripeAccount
{
    public function stripe(): \Stripe\StripeClient
    {
        return Stripe::client();
    }

    public function hasStripeAccount(): bool
    {
        return (bool) $this->stripe_account_id;
    }

    public function shouldCacheStripeAccount(): bool
    {
        return (bool) config('stripe.cache.accounts', false);
    }

    public function stripeAccountCacheKey(): string
    {
        return get_class($this).':'.$this->getKey().':'.'stripe:account';
    }

    public function cacheStripeAccount(?array $params = [], $opts = null): static
    {
        $this->forgetStripeAccount()->getStripeAccount($params, $opts);

        return $this;
    }

    public function forgetStripeAccount(): static
    {
        Cache::forget($this->stripeAccountCacheKey());

        return $this;
    }

    public function createStripeAccount(?array $params = [], $opts = null): \Stripe\Account
    {
        if ($this->stripe_account_id) {
            throw new Exception('['.get_class($this).':'.$this->getKey()."] Can't create, Stripe account already exists ({$this->stripe_account_id})");
        }

        $account = $this->stripe()->accounts->create([
            ...$params,
            'metadata' => [
                ...data_get($params, 'metadata', []),
                'model_type' => get_class($this),
                'model_id' => $this->getKey(),
            ],
        ], $opts);

        $this->importFromStripeAccount($account);

        if ($this->shouldCacheStripeAccount()) {
            Cache::put(
                key: $this->stripeAccountCacheKey(),
                value: $account,
                ttl: now()->addDay()
            );
        }

        return $account;
    }

    public function getStripeAccount(?array $params = [], $opts = null): ?\Stripe\Account
    {
        if (! $this->stripe_account_id) {
            return null;
        }

        if ($this->shouldCacheStripeAccount()) {
            return Cache::remember(
                key: $this->stripeAccountCacheKey(),
                ttl: now()->addDay(),
                callback: fn () => $this->getFreshStripeAccount($params, $opts)
            );
        }

        return $this->getFreshStripeAccount($params, $opts);
    }

    public function getFreshStripeAccount(?array $params = [], $opts = null): ?\Stripe\Account
    {
        if (! $this->stripe_account_id) {
            return null;
        }

        $account = $this->stripe()->accounts->retrieve($this->stripe_account_id, $params, $opts);
        $this->importFromStripeAccount($account);

        return $account;
    }

    public function updateStripeAccount(?array $params = null, $opts = null): \Stripe\Account
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'update acount');
        }

        $account = $this->stripe()->accounts->update($this->stripe_account_id, $params, $opts);

        $this->importFromStripeAccount($account);

        if ($this->shouldCacheStripeAccount()) {
            Cache::put(
                key: $this->stripeAccountCacheKey(),
                value: $account,
                ttl: now()->addDay()
            );
        }

        return $account;
    }

    public function updateStripeAccountCapability(string $capabilityId, ?array $params = [], $opts = null): \Stripe\Capability
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'update account capability');
        }

        $capability = $this->stripe()->accounts->updateCapability(
            $this->stripe_account_id,
            $capabilityId,
            $params,
            $opts
        );

        $this->forgetStripeAccount();

        return $capability;
    }

    public function deleteStripeAccount(?array $params = null, $opts = null): ?\Stripe\Account
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'delete acount');
        }

        $account = $this->stripe()->accounts->delete($this->stripe_account_id, $params, $opts);

        $this->importFromStripeAccount(null);

        $this->forgetStripeAccount();

        return $account;
    }

    public function createStripeLoginLink(?array $params = [], $opts = null): \Stripe\LoginLink
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'create login link');
        }

        return $this->stripe()->accounts->createLoginLink($this->stripe_account_id, $params, $opts);
    }

    public function createStripeAccountLink(?array $params = [], $opts = null): \Stripe\AccountLink
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'create account link');
        }

        return $this->stripe()->accountLinks->create(
            [
                ...$params,
                'account' => $this->stripe_account_id,
            ],
            $opts
        );
    }

    public function payoutStripeAccount(?array $params = [], $opts = []): \Stripe\Payout
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'payout');
        }

        return $this->stripe()->payouts->create($params, [
            ...$opts,
            'stripe_account' => $this->stripe_account_id,
        ]);
    }

    public function createStripeAccountTransfer(?array $params = [], $opts = []): \Stripe\Transfer
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'transfer');
        }

        return $this->stripe()->transfers->create([
            ...$params,
            'destination' => $this->stripe_account_id,
        ], $opts);
    }

    public function importFromStripeAccount(?\Stripe\Account $account): static
    {
        $this->stripe_account_id = $account?->id;

        $this->save();

        return $this;
    }
}
