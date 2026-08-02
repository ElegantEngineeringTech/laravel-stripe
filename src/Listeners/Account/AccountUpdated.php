<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners\Account;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Allows you to monitor changes to connected account requirements and status changes.
 * Available for Standard, Express, and Custom accounts.
 */
class AccountUpdated extends AbstractAccountListener implements ShouldQueue
{
    public function handle(WebhookCall $event): void
    {
        $account = $this->getStripeAccount($event);

        if (! $account) {
            return;
        }

        $model = $this->getAccountModel($account);

        if (! $model) {
            return;
        }

        $model->importFromStripeAccount($account); // @phpstan-ignore-line
    }
}
