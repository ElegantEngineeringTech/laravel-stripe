<?php

namespace Finller\Stripe\Listeners;

use Finller\Stripe\Traits\ListenAccountApplicationEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs when a user disconnects from your account and can be used to trigger required cleanup on your server.
 * Available for Standard accounts.
 */
class AccountApplicationDeauthorized implements ShouldQueue
{
    use ListenAccountApplicationEvents;

    public function __construct()
    {
        //
    }

    public function handle(WebhookCall $event): void
    {
        $model = $this->getModelFromEvent($event);

        if (!$model) {
            return;
        }

        // @phpstan-ignore-next-line
        $model
            ->importFromStripeAccount(null)
            ->forgetStripeAccount();
    }
}
