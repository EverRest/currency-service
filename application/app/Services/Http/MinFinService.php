<?php
declare(strict_types=1);

namespace App\Services\Http;

use App\DataTransferObjects\MinFinExchangeRate;
use App\Services\Eloquent\CurrencyService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use App\Services\Eloquent\BankService;

class MinFinService
{
    private const MIN_FIN_URL = 'https://minfin.com.ua/api/currency/';

    /**
     * @var Collection $availableCurrencies
     */
    private Collection $availableCurrencies;

    /**
     * @var Collection $availableBanks
     */
    private Collection $availableBanks;

    /**
     * @param CurrencyService $currencyService
     * @param BankService $bankService
     */
    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly BankService     $bankService,
    )
    {
        $this->availableCurrencies = $this->currencyService->list();
        $this->availableBanks = Collection::make(Config::get('banks'));
    }

    /**
     * Get all exchange rates for a specific bank without pagination.
     *
     * @param string $currency
     * @return array
     */
    public function getExchangeRatesByCurrencyCode(string $currency = ''): array
    {
        $allRates = [];
        $currentPage = 1;
        do {
            $response = $this->getExchangeRates($currency, $currentPage);
            if (Arr::has($response, 'data')) {
                $allRates = array_merge($allRates, Arr::get($response, 'data', []));
            }
            $currentPage++;
        } while (Arr::get($response, 'has_next_page'));

        return array_filter(
            $allRates,
            fn($rate) => $this->availableBanks
                ->contains(Arr::get($rate, 'slug')
                )
        );
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

    /**
     * @return array
     */
    public function getExchangeRatesDtoArray(): array
    {
        $dtoArray = [];
        foreach ($this->availableCurrencies as $currency) {
            $exchangeRates = $this->getExchangeRatesByCurrencyCode($currency->code);
            foreach ($exchangeRates as $exchangeRate) {
                $dto = new MinFinExchangeRate($exchangeRate, $currency->id, $this->bankService);
                $dtoArray[] = $dto;
            }
        }

        return $dtoArray;
    }
}
