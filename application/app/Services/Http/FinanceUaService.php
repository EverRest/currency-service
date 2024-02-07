<?php
declare(strict_types=1);

namespace App\Services\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class FinanceUaService
{
    private const FINANCE_UI_URL = 'https://finance.ua/';

    /**
     * @return Collection
     */
    public function getBankList(): Collection
    {
        $list = Collection::make();
        $response = Http::get(self::FINANCE_UI_URL . 'banks/api/organizationsList', [
            'locale' => 'uk',
        ]);
        $json = $response->json();
        if (Arr::has($json, 'responseData')) {
            $list->merge(Arr::get($json, 'responseData', []));
        }

        return $list;
    }

    /**
     * @param string $bankSlug
     * @param string $locale
     *
     * @return Collection
     */
    public function getBankBranchList(string $bankSlug, string $locale = 'uk'): Collection
    {
        $list = Collection::make();
        $response = Http::get(self::FINANCE_UI_URL . 'api/organization/v1/branches', [
            'slug' => $bankSlug,
            'locale' => $locale,
        ]);
        $json = $response->json();
        if (Arr::has($json, 'data')) {
            $list->merge(Arr::get($json, 'data', []));
        }

        return $list;
    }

    /**
     * @param string $bankSlug
     * @param string $locale
     *
     * @return Collection
     */
    public function getBankAtmList(string $bankSlug, string $locale = 'uk'): Collection
    {
        $list = Collection::make();
        $response = Http::get(self::FINANCE_UI_URL . 'api/organization/v1/atm', [
            'slug' => $bankSlug,
            'locale' => $locale,
        ]);
        $json = $response->json();
        if (Arr::has($json, 'data')) {
            $list->merge(Arr::get($json, 'data', []));
        }

        return $list;
    }
}
