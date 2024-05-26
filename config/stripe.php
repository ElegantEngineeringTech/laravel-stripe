<?php

use Elegant\Stripe\Commands\CreateStripeWebhooksCommand;
use Elegant\Stripe\ModelRepository;

return [

    'models' => [
        'accounts' => [
            \App\Models\User::class,
        ],
        'customers' => [
            \App\Models\User::class,
        ],
        'repository' => ModelRepository::class,
    ],

    'cache' => [
        'accounts' => true,
        'customers' => false,
    ],

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'version' => env('STRIPE_VERSION', '2024-04-10'),

    /**
     * This is only used for the CreateStripeWebhooksCommand
     * You can add more webhooks directly from your Stripe Dashboard
     */
    'webhooks' => [
        [
            'url' => '/webhooks/stripe',
            'connect' => false,
            'enabled_events' => [
                ...CreateStripeWebhooksCommand::DEFAULT_WEBHOOKS_EVENTS,
            ],
        ],
    ],

];
