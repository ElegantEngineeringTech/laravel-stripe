<?php

namespace Elegantly\Stripe\Traits;

use Elegantly\Stripe\ModelRepository;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Customer;
use Stripe\Event;

trait ListenCustomerEvents
{
    public function getStripeEvent(WebhookCall $event): ?Event
    {
        return $event->payload ?
            Event::constructFrom($event->payload) :
            null;
    }

    public function getStripeCustomerFromEvent(WebhookCall $event): ?Customer
    {
        return $this->getStripeEvent($event)?->data?->object; // @phpstan-ignore-line
    }

    public function getModelFromCustomer(Customer $customer): ?Model
    {
        return ModelRepository::findCustomerFromStripeObject($customer);
    }
}
