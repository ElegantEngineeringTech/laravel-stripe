<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class StripeCustomerAlreadyExistsException extends Exception
{
    public static function make(Model $model): self
    {
        return new self('['.get_class($model).':'.$model->getKey()."] Can't create, Stripe customer already exists ({$model->getAttribute('stripe_customer_id')})");
    }
}
