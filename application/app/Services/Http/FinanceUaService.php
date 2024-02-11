<?php
declare(strict_types=1);

namespace App\Services\Http;

use App\DataTransferObjects\FinanceUaBankBranch;
use App\Models\Bank;
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
        $response = Http::get(self::FINANCE_UI_URL . 'banks/api/organizationsList', [
            'locale' => 'uk',
        ]);

        return Collection::make(Arr::get($response->json(), 'responseData', []));
    }

    /**
     * @param string $bankSlug
     * @param string $locale
     *
     * @return Collection
     */
    public function getBankBranchList(string $bankSlug, string $locale = 'uk'): Collection
    {
        $response = Http::get(self::FINANCE_UI_URL . 'api/organization/v1/branches', [
            'slug' => $bankSlug,
            'locale' => $locale,
        ]);

        return Collection::make(Arr::get($response->json(), 'data', []));
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
            $list = Collection::make(Arr::get($json, 'data', []));
        }

        return $list;
    }

    /**
     * @param Bank $bank
     *
     * @return array
     */
    public function getBankBranchesDtoArray(Bank $bank): array
    {
        $bankBranches = [];
        foreach ($this->getBankBranchList($bank->code) as $branch) {
            $localBranches = Arr::get($branch, 'data');
            $dtoData = array_map(function ($b) use ($bank, $branch) {
                Arr::set($b, 'bank_id', $bank->id);
                Arr::set($b, 'external_id', Arr::get($branch, 'id'));
                return $b;
            }, $localBranches);
            $bankBranches = array_merge($bankBranches, $dtoData);
        }

        return FinanceUaBankBranch::collect($bankBranches);
    }
}
