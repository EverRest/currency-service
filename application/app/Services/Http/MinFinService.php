<?php
declare(strict_types=1);

namespace App\Services\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MinFinService
{
    private const MIN_FIN_URL = 'https://minfin.com.ua/api/currency/';

    /**
     * Get all exchange rates for a specific bank without pagination.
     *
     * @param string $slug
     * @return Collection
     */
    public function getAllExchangeRates(string $slug = ''): Collection
    {
        $allRates = Collection::empty();
        $currentPage = 1;

        do {
            $response = $this->getExchangeRates($slug, $currentPage);
            if (Arr::has($response, 'data')) {
                $allRates = $allRates->merge(Arr::get($response, 'data'));
            }
            $currentPage++;

        } while (Arr::get($response, 'has_next_page'));

        return $allRates;
    }

    /**
     * Get exchange rates for a specific bank with pagination.
     *
     * @param string $slug
     * @param int $page
     * @return array
     */
    private function getExchangeRates(string $slug = '', int $page = 1): array
    {
        $response = Http::get(self::MIN_FIN_URL . "rates/banks/$slug", compact('slug', 'page'));
        $json = $response->json();

        return [
            'data' => Arr::get($json, 'data', []),
            'has_next_page' => Arr::get($json, 'meta.next', 0),
        ];
    }

    /**
     * Get the list of currencies.
     *
     * @param string $type
     * @param string $locale
     * @return Collection
     */
    public function getCurrencyList(string $type = 'money', string $locale = 'uk'): Collection
    {
        $list = Collection::empty();
        $response = Http::get(self::MIN_FIN_URL . 'currency/list', compact('locale', 'type'));
        $json = $response->json();

        if (Arr::has($json, 'list')) {
            $list = Collection::make(Arr::get($json, 'list', []));
        }

        return $list;
    }
}
