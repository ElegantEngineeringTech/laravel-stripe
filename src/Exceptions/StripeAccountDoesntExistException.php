<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class StripeAccountDoesntExistException extends Exception
{
    public static function make(Model $model, string $operation): self
    {
        return new self('['.get_class($model).':'.$model->getKey()."] Can't {$operation}, Stripe account doesn't exist");
    }
}
