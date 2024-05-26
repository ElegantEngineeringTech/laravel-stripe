<?php

namespace Elegant\Stripe\Traits;

use Elegant\Stripe\ModelRepository;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Account;
use Stripe\Event;

trait ListenAccountEvents
{
    public function getStripeEvent(WebhookCall $event): ?Event
    {
        return $event->payload ?
            Event::constructFrom($event->payload) :
            null;
    }

    public function getStripeAccountFromEvent(WebhookCall $event): ?Account
    {
        return $this->getStripeEvent($event)?->data?->object; // @phpstan-ignore-line
    }

    public function getModelFromAccount(Account $account): ?Model
    {
        return ModelRepository::findAccountFromStripeObject($account);
    }
}
