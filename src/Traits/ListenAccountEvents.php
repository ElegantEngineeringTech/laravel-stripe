<?php

namespace Finller\Stripe\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Account;
use Stripe\Event;

trait ListenAccountEvents
{
    public function getStripeAccountFromEvent(WebhookCall $event): ?Account
    {
        return Event::constructFrom($event->payload)->data?->object; // @phpstan-ignore-line
    }

    public function getModelFromAccount(Account $account): ?Model
    {
        $model_type = data_get($account->metadata, 'model_type');
        $model_id = data_get($account->metadata, 'model_id');

        /** @var ?Model $model */
        $model = $model_type::find($model_id);

        if (! $model) {
            return null;
        }

        /** @var ?string $model_stripe_account_id */
        $model_stripe_account_id = $model->stripe_account_id; // @phpstan-ignore-line

        if ($model_stripe_account_id !== $account->id) {
            throw new Exception("[{$model_type}:{$model_id}] Conflict between Stripe account ID and Stripe account metadata: {$model_stripe_account_id} !== {$account->id}", 500);
        }

        return $model;
    }
}
