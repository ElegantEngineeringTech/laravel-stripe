<?php

namespace Elegantly\Stripe;

class Stripe
{
    public \Stripe\StripeClient $client;

    public function __construct(
        string $key,
        ?string $version = null
    ) {
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
