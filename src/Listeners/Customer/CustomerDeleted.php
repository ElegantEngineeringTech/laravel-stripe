<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners\Customer;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs whenever a customer is deleted.
 */
class CustomerDeleted extends AbstractCustomerListener implements ShouldQueue
{
    public function handle(WebhookCall $event): void
    {
        $customer = $this->getStripeCustomer($event);

        if (! $customer) {
            return;
        }

        $model = $this->getCustomerModel($customer);

        if (! $model) {
            return;
        }

        $model->importFromStripeCustomer(null); // @phpstan-ignore-line
    }
}
