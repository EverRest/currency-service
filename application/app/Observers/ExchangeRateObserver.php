<?php
declare(strict_types=1);

namespace App\Observers;

use App\Events\ExchangeRateChanged;
use App\Models\ExchangeRate;

class ExchangeRateObserver
{
    /**
     * @param ExchangeRate $ExchangeRate
     *
     * @return void
     */
    public function created(ExchangeRate $ExchangeRate): void
    {
        ExchangeRateChanged::dispatch($ExchangeRate);
    }
}
