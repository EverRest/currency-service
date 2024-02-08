<?php
declare(strict_types=1);

namespace App\Traits;

trait HasGetRateChangeInPercents
{
    /**
     * Get the rate change in percents.
     *
     * @param float $previousRate
     * @param float $newRate
     *
     * @return float
     */
    protected function getRateChangeInPercents(float $previousRate, float $newRate): float
    {
        return $previousRate ? (($newRate - $previousRate) / $previousRate) * 100 : 0;
    }
}
