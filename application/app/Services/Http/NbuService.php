<?php
declare(strict_types=1);

namespace App\Services\Http;

use App\DataTransferObjects\NbuExchangeRate;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class NbuService
{
    private const NBU_URL = 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json';
    private const NBU_CURRENCY_CODE_KEY = 'cc';

    /**
     * @var Collection $availableCurrencies
     */
    private Collection $availableCurrencies;

    /**
     * NbuService constructor.
     *
     */
    public function __construct()
    {
            $this->availableCurrencies = Collection::make(
                array_keys(
                    Config::get('currencies')
                )
            );
    }

    /**
     * @return array
     */
    public function getExchangeRates(): array
    {
        $response = Http::get(self::NBU_URL);

        return array_filter(
            $response->json(),
            function (array $rate) {
                return $this->availableCurrencies
                    ->contains(Arr::get($rate, self::NBU_CURRENCY_CODE_KEY));
            }
        );
    }

    /**
     * @return array
     */
    public function getExchangeRatesDtoArray(): array
    {
        $rates = $this->getExchangeRates();

        return NbuExchangeRate::collect($rates);
    }
}
