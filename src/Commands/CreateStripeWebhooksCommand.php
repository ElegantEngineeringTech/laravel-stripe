<?php

namespace Finller\Stripe\Commands;

use Finller\Stripe\Facades\Stripe;
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

        /** @var array $webhooks */
        $webhooks = config('stripe.webhooks');

        foreach ($webhooks as $webhooks) {
            $stripe->webhookEndpoints->create(array_filter([
                'enabled_events' => data_get($webhooks, 'enabled_events'),
                'url' => route(data_get($webhooks, 'url')),
                'api_version' => data_get($webhooks, 'api_version', config('stripe.version')),
                'connect' => data_get($webhooks, 'connect'),
            ]));
        }

        $this->info('The Stripe webhook was created successfully. Retrieve the webhook secret in your Stripe dashboard and define it as an environment variable.');

        return self::SUCCESS;
    }
}
