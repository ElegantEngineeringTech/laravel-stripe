<?php

declare(strict_types=1);

namespace Elegantly\Stripe;

use Elegantly\Stripe\Facades\Stripe;
use Exception;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\AccountSession;
use Stripe\Capability;
use Stripe\LoginLink;
use Stripe\Payout;
use Stripe\StripeClient;
use Stripe\Transfer;

/**
 * @property ?string $stripe_account_id
 */
trait HasStripeAccount
{
    public function stripe(): StripeClient
    {
        return Stripe::client();
    }

    public function hasStripeAccount(): bool
    {
        return (bool) $this->stripe_account_id;
    }

    public function createStripeAccount(?array $params = [], $opts = null): Account
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

        return $account;
    }

    public function getStripeAccount(?array $params = [], $opts = null): ?Account
    {
        if (! $this->stripe_account_id) {
            return null;
        }

        $account = $this->stripe()->accounts->retrieve($this->stripe_account_id, $params, $opts);
        $this->importFromStripeAccount($account);

        return $account;
    }

    public function updateStripeAccount(?array $params = null, $opts = null): Account
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'update acount');
        }

        $account = $this->stripe()->accounts->update($this->stripe_account_id, $params, $opts);

        $this->importFromStripeAccount($account);

        return $account;
    }

    public function updateStripeAccountTaxSettings(?array $params = null, $opts = null)
    {

        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'update acount');
        }

        $settings = $this->stripe()->tax->settings->update(
            $params,
            [...$opts, 'stripe_account' => $this->stripe_account_id]
        );

        return $settings;

    }

    public function updateStripeAccountCapability(string $capabilityId, ?array $params = [], $opts = null): Capability
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

        return $capability;
    }

    public function deleteStripeAccount(?array $params = null, $opts = null): ?Account
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'delete acount');
        }

        $account = $this->stripe()->accounts->delete($this->stripe_account_id, $params, $opts);

        $this->importFromStripeAccount(null);

        return $account;
    }

    public function createStripeLoginLink(?array $params = [], $opts = null): LoginLink
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'create login link');
        }

        return $this->stripe()->accounts->createLoginLink($this->stripe_account_id, $params, $opts);
    }

    public function createStripeAccountLink(?array $params = [], $opts = null): AccountLink
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

    public function createStripeAccountSession(?array $params = [], $opts = null): AccountSession
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'create account session');
        }

        return $this->stripe()->accountSessions->create(
            [
                ...$params,
                'account' => $this->stripe_account_id,
            ],
            $opts
        );
    }

    public function payoutStripeAccount(?array $params = [], $opts = []): Payout
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'payout');
        }

        return $this->stripe()->payouts->create(
            $params,
            [...$opts, 'stripe_account' => $this->stripe_account_id]
        );
    }

    public function createStripeAccountTransfer(?array $params = [], $opts = []): Transfer
    {
        if (! $this->stripe_account_id) {
            throw StripeAccountDoesntExistExecption::make($this, 'transfer');
        }

        return $this->stripe()->transfers->create([
            ...$params,
            'destination' => $this->stripe_account_id,
        ], $opts);
    }

    public function importFromStripeAccount(?Account $account): static
    {
        $this->stripe_account_id = $account?->id;

        $this->save();

        return $this;
    }
}
