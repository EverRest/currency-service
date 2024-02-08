<?php
declare(strict_types=1);

namespace App\Services\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NbuService
{
    private const NBU_URL = 'https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json';

    /**
     * @return Collection
     */
    public function getExchangeRates(): Collection
    {
        $response = Http::get(self::NBU_URL);

        return Collection::make($response->json());
    }
}
