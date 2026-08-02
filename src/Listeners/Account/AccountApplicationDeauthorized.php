<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners\Account;

use Elegantly\Stripe\Listeners\AbstractStripeListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Application;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs when a user disconnects from your account and can be used to trigger required cleanup on your server.
 * Available for Standard accounts.
 */
class AccountApplicationDeauthorized extends AbstractStripeListener implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(WebhookCall $event): void
    {
        $model = $this->getAccountModel($event);

        if (! $model) {
            return;
        }

        // @phpstan-ignore-next-line
        $model->importFromStripeAccount(null);
    }

    public function getStripeApplicationFromEvent(WebhookCall $event): ?Application
    {
        return $this->getStripeEvent($event)?->data?->object; // @phpstan-ignore-line
    }

    public function getAccountModel(WebhookCall $event): ?Model
    {
        $stripeEvent = $this->getStripeEvent($event);

        if (! $stripeEvent?->account) {
            return null;
        }

        $repository = $this->getModelRepository();

        return $repository::findAccount($stripeEvent->account);
    }
}
