<?php
declare(strict_types=1);

namespace App\Services\Helper;

use App\Models\ExchangeRate;

class CriticalRateChangeChecker
{
    private const CRITICAL_RATE_CHANGE_CRITERIA = 5;

    /**
     * @param ExchangeRate|null $previousExchangeRate
     * @param ExchangeRate $newExchangeRate
     *
     * @return bool
     */
    public function check(?ExchangeRate $previousExchangeRate, ExchangeRate $newExchangeRate): bool
    {
        if (!$previousExchangeRate) {
            return false;
        }

        return abs($previousExchangeRate->bid - $newExchangeRate->bid) >= self::CRITICAL_RATE_CHANGE_CRITERIA ||
            abs($previousExchangeRate->bid - $newExchangeRate->bid) >= self::CRITICAL_RATE_CHANGE_CRITERIA;
    }
}
