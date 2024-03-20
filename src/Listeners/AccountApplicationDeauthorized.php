<?php

namespace Finller\Stripe\Listeners;

use Finller\Stripe\Traits\ListenAccountEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Account;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs when a user disconnects from your account and can be used to trigger required cleanup on your server.
 * Available for Standard accounts.
 */
class AccountApplicationDeauthorized implements ShouldQueue
{
    use ListenAccountEvents;

    public function __construct()
    {
        //
    }

    public function handle(WebhookCall $event): void
    {
        $account = $this->getStripeAccountFromEvent($event);

        if (! $account) {
            return;
        }

        $model = $this->getModelFromAccount($account);

        $model->importFromStripeAccount(null); // @phpstan-ignore-line
        $model->forgetStripeAccount(); // @phpstan-ignore-line
    }
}
