<?php

// config for Finller/Stripe
return [

    'table' => [
        'account' => 'users',
    ],

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'version' => env('STRIPE_VERSION'),

];
