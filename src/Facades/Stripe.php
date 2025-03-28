<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Elegantly\Stripe\Stripe
 *
 * @method static \Stripe\StripeClient client()
 */
class Stripe extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Elegantly\Stripe\Stripe::class;
    }
}
