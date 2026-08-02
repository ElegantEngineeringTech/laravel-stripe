<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners\Customer;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs whenever any property of a customer changes.
 */
class CustomerUpdated extends AbstractCustomerListener implements ShouldQueue
{
    public function __construct()
    {
        //
    }

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

        $model->importFromStripeCustomer($customer); // @phpstan-ignore-line

    }
}
