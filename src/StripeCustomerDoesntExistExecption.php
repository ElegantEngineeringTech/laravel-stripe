<?php

namespace Elegant\Stripe;

use Exception;
use Illuminate\Database\Eloquent\Model;

class StripeCustomerDoesntExistExecption extends Exception
{
    public static function make(Model $model, string $action): self
    {
        return new self('['.get_class($model).':'.$model->getKey()."] Can't {$action}, Stripe customer does not exist");
    }
}
