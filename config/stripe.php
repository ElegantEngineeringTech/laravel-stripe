<?php

// config for Finller/Stripe
return [

    'tables' => [
        'accounts' => 'users',
        'customers' => 'users',
    ],

    'cache' => [
        'accounts' => true,
        'customers' => false,
    ],

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'version' => env('STRIPE_VERSION'),

    /**
     * This is only used for the CreateStripeWebhooksCommand
     * You can add more webhooks directly from your Stripe Dashboard
     */
    'webhooks' => [
        [
            'url' => '/webhooks/stripe',
            'connect' => false,
            'enabled_events' => [
                'customer.updated',
                // Stripe Connect webhooks
                'account.updated',
                'account.application.deauthorized',
            ],
        ],
    ],

];
