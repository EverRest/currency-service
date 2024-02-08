<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\CurrencyRateChanged;

class SubscriptionCheck
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CurrencyRateChanged $event): void
    {
        //
    }
}
