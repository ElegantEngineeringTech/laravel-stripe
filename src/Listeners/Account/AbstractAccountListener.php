<?php

declare(strict_types=1);

namespace Elegantly\Stripe\Listeners\Account;

use Elegantly\Stripe\Listeners\AbstractStripeListener;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Account;

abstract class AbstractAccountListener extends AbstractStripeListener
{
    public function getStripeAccount(WebhookCall $event): ?Account
    {
        return $this->getStripeEvent($event)?->data?->object; // @phpstan-ignore-line
    }

    public function getAccountModel(Account $customer): ?Model
    {
        $repository = $this->getModelRepository();

        return $repository::findAccountFromStripeObject($customer);
    }
}
