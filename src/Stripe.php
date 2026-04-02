<?php

declare(strict_types=1);

namespace Elegantly\Stripe;

use Stripe\StripeClient;

class Stripe
{
    public StripeClient $client;

    public function __construct(
        string $key,
        ?string $version = null
    ) {
        $this->client = new StripeClient(array_filter([
            'api_key' => $key,
            'stripe_version' => $version, // By default Stripe will use the latest version available
        ]));
    }

    public function client(): StripeClient
    {
        return $this->client;
    }
}
