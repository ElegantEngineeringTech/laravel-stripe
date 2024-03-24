<?php

namespace Finller\Stripe\Traits;

use Finller\Stripe\ModelRepository;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Application;
use Stripe\Event;

trait ListenAccountApplicationEvents
{
    public function getStripeEvent(WebhookCall $event): ?Event
    {
        return $event->payload ?
            Event::constructFrom($event->payload) :
            null;
    }

    public function getStripeApplicationFromEvent(WebhookCall $event): ?Application
    {
        return $this->getStripeEvent($event)?->data?->object; // @phpstan-ignore-line
    }

    public function getModelFromEvent(WebhookCall $event): ?Model
    {
        $stripeEvent = $this->getStripeEvent($event);

        if (!$stripeEvent?->account) {
            return null;
        }

        return ModelRepository::findAccount($stripeEvent->account);
    }
}
