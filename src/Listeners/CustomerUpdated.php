<?php

namespace Finller\Stripe\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Customer;
use Stripe\Event;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs whenever any property of a customer changes.
 */
class CustomerUpdated implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(WebhookCall $event)
    {
        /** @var ?Customer $account */
        $customer = Event::constructFrom($event->payload)->data?->object; // @phpstan-ignore-line

        if (! $customer) {
            return;
        }

        $model_type = data_get($customer->metadata, 'model_type');
        $model_id = data_get($customer->metadata, 'model_id');

        /** @var Model $model */
        $model = $model_type::findOrFail($model_id);

        /** @var ?string $model_stripe_account_id */
        $model_stripe_customer_id = $model->stripe_customer_id; // @phpstan-ignore-line

        if ($model_stripe_customer_id !== $customer->id) {
            throw new Exception("[{$model_type}:{$model_id}] Conflict between Stripe customer ID and Stripe customer metadata: {$model_stripe_customer_id} !== {$customer->id}", 500);
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
