<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners\Customer;

use Elegantly\Stripe\Listeners\AbstractStripeListener;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Customer;

abstract class AbstractCustomerListener extends AbstractStripeListener
{
    public function getStripeCustomer(WebhookCall $event): ?Customer
    {
        return $this->getStripeEvent($event)?->data?->object; // @phpstan-ignore-line
    }

    public function getCustomerModel(Customer $customer): ?Model
    {
        $repository = $this->getModelRepository();

        return $repository::findCustomerFromStripeObject($customer);
    }
}
