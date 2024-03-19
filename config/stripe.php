<?php

// config for Finller/Stripe
return [

    'table' => [
        'account' => 'users',
    ],

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'version' => env('STRIPE_VERSION'),

    'webhooks' => [
        [
            'url' => "/webhooks/stripe",
            'connect' => false,
            "enabled_events" => [
                'account.updated',
                'account.application.deauthorized'
            ]
        ]
    ],

];
