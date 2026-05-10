<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners;

use Elegantly\Stripe\Traits\ListenCustomerEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs whenever any property of a customer changes.
 */
class CustomerUpdated implements ShouldQueue
{
    use ListenCustomerEvents;

    public function __construct()
    {
        //
    }

    public function handle(WebhookCall $event): void
    {
        $customer = $this->getStripeCustomerFromEvent($event);

        if (! $customer) {
            return;
        }

        $model = $this->getModelFromCustomer($customer);

        if (! $model) {
            return;
        }

        $model->importFromStripeCustomer($customer); // @phpstan-ignore-line

    }
}
