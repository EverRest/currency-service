<?php
declare(strict_types=1);

namespace App\Observers;

use App\Events\CurrencyRateChanged;
use App\Models\CurrencyRate;

class CurrencyRateObserver
{
    /**
     * @param CurrencyRate $currencyRate
     *
     * @return void
     */
    public function created(CurrencyRate $currencyRate): void
    {
        CurrencyRateChanged::dispatch($currencyRate);
    }
}
