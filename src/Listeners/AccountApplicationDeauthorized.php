<?php

namespace Finller\Stripe\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Account;
use Stripe\Event;

/**
 * @see https://docs.stripe.com/connect/webhooks
 *
 * Occurs when a user disconnects from your account and can be used to trigger required cleanup on your server.
 * Available for Standard accounts.
 */
class AccountApplicationDeauthorized implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(WebhookCall $event)
    {
        /** @var ?Account $account */
        $account = Event::constructFrom($event->payload)->data?->object; // @phpstan-ignore-line

        if (!$account) {
            return;
        }

        $model_type = data_get($account->metadata, 'model_type');
        $model_id = data_get($account->metadata, 'model_id');

        /** @var Model $model */
        $model = $model_type::findOrFail($model_id);

        /** @var ?string $model_stripe_account_id */
        $model_stripe_account_id = $model->stripe_account_id; // @phpstan-ignore-line

        if ($model_stripe_account_id !== $account->id) {
            throw new Exception("[{$model_type}:{$model_id}] Conflict between Stripe account ID and Stripe account metadata: {$model_stripe_account_id} !== {$account->id}", 500);
        }

        $model->syncWithStripeAccount(null); // @phpstan-ignore-line
        $model->forgetStripeAccount(); // @phpstan-ignore-line
    }
}
