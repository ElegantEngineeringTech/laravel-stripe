<?php

namespace Finller\Stripe;

use Exception;
use Illuminate\Database\Eloquent\Model;

class StripeAccountDoesntExistExecption extends Exception
{
    public static function make(Model $model, string $action): self
    {
        return new self('[' . get_class($model) . ':' . $model->getKey() . "] Can't {$action}, Stripe account does not exist");
    }
}
