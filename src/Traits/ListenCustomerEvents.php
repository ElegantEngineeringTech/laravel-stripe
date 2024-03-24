<?php

namespace Finller\Stripe\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Customer;
use Stripe\Event;
use Stripe\StripeObject;

trait ListenCustomerEvents
{
    /**
     * @return ?Customer
     */
    public function getStripeCustomerFromEvent(WebhookCall $event): ?StripeObject
    {
        return Event::constructFrom($event->payload)->data?->object; // @phpstan-ignore-line
    }

    public function getModelFromCustomer(Customer $customer): ?Model
    {
        $model_type = data_get($customer->metadata, 'model_type');
        $model_id = data_get($customer->metadata, 'model_id');

        /** @var ?Model $model */
        $model = $model_type::find($model_id);

        if (! $model) {
            return null;
        }

        /** @var ?string $model_stripe_account_id */
        $model_stripe_customer_id = $model->stripe_customer_id; // @phpstan-ignore-line

        if ($model_stripe_customer_id !== $customer->id) {
            throw new Exception("[{$model_type}:{$model_id}] Conflict between Stripe customer ID and Stripe customer metadata: {$model_stripe_customer_id} !== {$customer->id}", 500);
        }

        return $model;
    }
}
