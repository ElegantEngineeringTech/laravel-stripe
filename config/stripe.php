<?php

declare(strict_types=1);

use App\Models\User;
use Elegantly\Stripe\Commands\CreateStripeWebhooksCommand;
use Elegantly\Stripe\ModelRepository;

return [

    'models' => [
        'repository' => ModelRepository::class,

        'accounts' => [
            User::class => 'stripe_account_id',
        ],
        'customers' => [
            User::class => 'stripe_customer_id',
        ],
    ],

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe API version
    |--------------------------------------------------------------------------
    |
    | Leave to null to use the latest API version.
    |
    */
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
                ...CreateStripeWebhooksCommand::DEFAULT_WEBHOOKS_EVENTS,
            ],
        ],
    ],

];
