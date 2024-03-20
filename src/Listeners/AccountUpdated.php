<?php

namespace Finller\Stripe\Listeners;

use Finller\Stripe\Traits\ListenAccountEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Allows you to monitor changes to connected account requirements and status changes.
 * Available for Standard, Express, and Custom accounts.
 */
class AccountUpdated implements ShouldQueue
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

        $model->importFromStripeAccount($account); // @phpstan-ignore-line

        if ($model->shouldCacheStripeAccount()) { // @phpstan-ignore-line
            Cache::forever(
                $model->stripeAccountCacheKey(), // @phpstan-ignore-line
                $account
            );
        }
    }
}
