<?php

namespace Elegantly\Stripe;

class Stripe
{
    public \Stripe\StripeClient $client;

    public function __construct(
        string $key,
        ?string $version = null
    ) {
        $this->client = new \Stripe\StripeClient(array_filter([
            'api_key' => $key,
            'stripe_version' => $version, // By default Stripe will use the latest version available
        ]));
    }

    public function client(): \Stripe\StripeClient
    {
        return $this->client;
    }
}
