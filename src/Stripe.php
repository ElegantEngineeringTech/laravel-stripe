<?php

namespace Finller\Stripe;

class Stripe
{
    public \Stripe\StripeClient $client;

    public function __construct(
        ?string $key = null,
        ?string $version = null
    ) {
        $key ??= config('stripe.key');
        $version ??= config('stripe.version');

        $this->client = new \Stripe\StripeClient([
            'api_key' => $key,
            'stripe_version' => $version,
        ]);
    }

    public function client(): \Stripe\StripeClient
    {
        return $this->client;
    }
}
