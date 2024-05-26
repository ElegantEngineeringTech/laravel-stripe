<?php

namespace Elegant\Stripe\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Elegant\Stripe\Stripe
 *
 * @method static \Stripe\StripeClient client()
 */
class Stripe extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Elegant\Stripe\Stripe::class;
    }
}
