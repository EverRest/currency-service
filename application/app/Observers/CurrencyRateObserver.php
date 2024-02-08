<?php
declare(strict_types=1);

namespace App\Observers;

use App\Models\CurrencyRate;

class CurrencyRateObserver
{
    /**
     * Handle the CurrencyRate "created" event.
     */
    public function created(CurrencyRate $currencyRate): void
    {
        // ...
    }
}
