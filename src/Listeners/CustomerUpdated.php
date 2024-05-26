<?php

namespace Elegant\Stripe\Listeners;

use Elegant\Stripe\Traits\ListenCustomerEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
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

        if ($model->shouldCacheStripeCustomer()) { // @phpstan-ignore-line
            Cache::forever(
                $model->stripeCustomerCacheKey(), // @phpstan-ignore-line
                $customer
            );
        }
    }
}
