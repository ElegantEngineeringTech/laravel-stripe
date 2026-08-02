<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners;

use Elegantly\Stripe\ModelRepository;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Event;

abstract class AbstractStripeListener
{
    abstract public function handle(WebhookCall $webhookCall): void;

    public function getStripeEvent(WebhookCall $webhookCall): ?Event
    {
        return $webhookCall->payload ? Event::constructFrom($webhookCall->payload) : null;
    }

    /**
     * @return class-string<ModelRepository>
     */
    public function getModelRepository(): string
    {
        return config('stripe.models.repository') ?? ModelRepository::class;
    }
}
