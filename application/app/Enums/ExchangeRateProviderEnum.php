<?php
declare(strict_types=1);

namespace App\Enums;

use App\Services\Http\MinFinService;
use App\Services\Http\NbuService;
use Exception;

/**
 * @method static self NBU()
 * @method static self MinFin()
 */
enum ExchangeRateProviderEnum
{
    const NBU = 'nbu';
    const MinFin = 'minfin';

    /**
     * Convert the enum to an array.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [self::NBU, self::MinFin];
    }

    /**
     * Get the service class name by the enum value.
     *
     * @param string $rateProvider
     *
     * @return string
     * @throws Exception
     */
    public static function getHttpServicePath(string $rateProvider): string
    {
        return match ($rateProvider) {
            ExchangeRateProviderEnum::NBU => NbuService::class,
            ExchangeRateProviderEnum::MinFin => MinFinService::class,
            default => throw new Exception("Invalid provider $rateProvider."),
        };
    }
}
