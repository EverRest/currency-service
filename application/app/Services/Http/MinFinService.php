<?php
declare(strict_types=1);

namespace App\Services\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MinFinService
{
    private const MIN_FIN_URL = 'https://minfin.com.ua/currency/';

    /**
     * @param string $slug
     *
     * @return Collection
     */
    public function getExchangeRates(string $slug = ''): Collection
    {
        $rates = Collection::make();
        $response = Http::withUrlParameters([
            'endpoint' => self::MIN_FIN_URL . '/rates/banks',
            'slug' => $slug,
        ])->get('{+endpoint}/{slug}');
        $json = $response->json();
        if (Arr::has($json, 'data')) {
            $rates->merge(Arr::get($json, 'data', []));
        }

        return $rates;
    }

    /**
     * @param string $type
     * @param string $locale
     *
     * @return Collection
     */
    public function getCurrencyList(string $type = 'money', string $locale = 'uk'): Collection
    {

        $list = Collection::make();
        $response = Http::get(self::MIN_FIN_URL . 'currency/list', [
            'locale' => $locale,
            'type' => $type,
        ]);
        $json = $response->json();
        if (Arr::has($json, 'list')) {
            $list->merge(Arr::get($json, 'list', []));
        }

        return $list;
    }
}
