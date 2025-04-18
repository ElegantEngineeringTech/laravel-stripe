<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Commands;

use Elegantly\Stripe\Facades\Stripe;
use Illuminate\Console\Command;

class CreateStripeWebhooksCommand extends Command
{
    public $signature = 'stripe:create-webhooks';

    public $description = 'Create Stripe webhooks';

    public const DEFAULT_WEBHOOKS_EVENTS = [
        'account.updated',
        'account.application.deauthorized',
        'customer.updated',
        'customer.deleted',
    ];

    public function handle(): int
    {
        $stripe = Stripe::client();

        /**
         * @var array<int, array{api_version?: string, connect?: bool, description?: null|string, enabled_events: string[], expand?: string[], metadata?: null|\Stripe\StripeObject, url: string}> $webhooks
         */
        $webhooks = config('stripe.webhooks');

        foreach ($webhooks as $webhook) {
            $stripeWebhook = $stripe->webhookEndpoints->create(array_filter([
                'enabled_events' => $webhook['enabled_events'],
                'url' => url($webhook['url']),
                'api_version' => $webhook['api_version'] ?? null,
                'connect' => $webhook['connect'] ?? null,
                'description' => $webhook['description'] ?? 'Created with elegantly/laravel-stripe',
            ], fn ($item) => $item !== null));

            $stripe->webhookEndpoints->update($stripeWebhook->id, [
                'disabled' => true,
            ]);
        }

        $this->info('The Stripe webhook was created successfully, next:');
        $this->line('1. Retrieve the webhook secret in your Stripe dashboard and define it as an environment variable.');
        $this->line('2. Enable the webhooks in your Stripe dashboard');
        $this->line('3. If you are migrating the Stripe api version, delete your old webhooks endpoints in your Stripe dashboard');

        return self::SUCCESS;
    }
}
