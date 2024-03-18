<?php

namespace Finller\Stripe\Commands;

use Illuminate\Console\Command;

class StripeCommand extends Command
{
    public $signature = 'laravel-stripe';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
