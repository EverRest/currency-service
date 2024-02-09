<?php
declare(strict_types=1);

namespace App\Services\Http;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NbuService
{
    private const NBU_URL = 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json';

    /**
     * @return array
     */
    public function getExchangeRates(): array
    {
        $response = Http::get(self::NBU_URL);

        return array_map(function ($rate) {
            return [
                'currency_id' => $rate['r030'],
                'bid' => $rate['rate'],
                'ask' => $rate['rate'],
                'date' => Carbon::parse($rate['exchangedate']),
                'currency_code' => $rate['cc'],
                'currency_name' => $rate['txt'],
            ];
        }, $response->json());
    }
}
