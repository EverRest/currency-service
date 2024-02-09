<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\Bank;
use App\Models\Currency;
use App\Services\Eloquent\BankService;
use App\Services\Eloquent\CurrencyRateService;
use App\Services\Eloquent\CurrencyService;
use App\Services\Http\MinFinService;
use App\Services\Http\NbuService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class StoreCurrentRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param BankService $bankService
     * @param CurrencyService $currencyService
     * @param CurrencyRateService $currencyRateService
     * @param MinFinService $minFinService
     * @param NbuService $nbuService
     *
     * @return void
     */
    public function handle(
        BankService         $bankService,
        CurrencyService     $currencyService,
        CurrencyRateService $currencyRateService,
        MinFinService       $minFinService,
        NbuService          $nbuService
    ): void
    {
        $banks = $bankService->list();
        $bankCodes = $banks->pluck('code')->toArray();
        $currencyService->query()
            ->each(
                fn($currency) => $this->storeCurrencyRates(
                    $currency,
                    $banks,
                    $bankCodes,
                    $currencyRateService,
                    $minFinService,
                    $nbuService
                )
            );
    }

    /**
     * @param Currency $currency
     * @param Collection $banks
     * @param array $bankCodes
     * @param CurrencyRateService $currencyRateService
     * @param MinFinService $minFinService
     * @param NbuService $nbuService
     *
     * @return void
     */
    private function storeCurrencyRates(
        Currency $currency,
        Collection $banks,
        array $bankCodes,
        CurrencyRateService $currencyRateService,
        MinFinService $minFinService,
        NbuService $nbuService
    ): void {
        $allCurrentCurrencyRates = $minFinService->getAllExchangeRates($currency->code);
        $nbuBank = $banks->firstWhere('code', 'nbu');
        $desiredBankCodes = Config::get('banks');
        $this->storeRatesForBanks(
            $currency,
            $allCurrentCurrencyRates,
            $desiredBankCodes,
            $banks,
            $nbuBank,
            $currencyRateService,
        );
        $this->storeNbuRates($currency,$nbuBank, $nbuService, $currencyRateService);
    }

    /**
     * @param Currency $currency
     * @param Collection $allCurrentCurrencyRates
     * @param array $desiredBankCodes
     * @param Collection $banks
     * @param Bank $nbuBank
     * @param CurrencyRateService $currencyRateService
     *
     * @return void
     */
    private function storeRatesForBanks(
        Currency $currency,
        Collection $allCurrentCurrencyRates,
        array $desiredBankCodes,
        Collection $banks,
        Bank $nbuBank,
        CurrencyRateService $currencyRateService
    ): void {
        $allCurrentCurrencyRates->each(
            function ($rate) use ($currency, $banks, $desiredBankCodes, $nbuBank, $currencyRateService) {
                $bankCode = Arr::get($rate, 'slug');
                if (in_array($bankCode, $desiredBankCodes, true)) {
                    $rateExists = $this->rateExists($currency, $rate, $bankCode, $banks, $nbuBank, $currencyRateService);
                    if (!$rateExists) {
                        $this->storeCurrencyRate($currency, $rate, $bankCode, $banks, $nbuBank, $currencyRateService);
                    }
                }
            }
        );
    }

    private function storeNbuRates(
        Currency $currency,
        Bank $nbuBank,
        NbuService $nbuService,
        CurrencyRateService $currencyRateService
    ): void {
        $nbuRates = $nbuService->getExchangeRates();
        foreach ($nbuRates as $nbuRate) {
            $rateExists = $this->rateExists($currency, ['cash' => $nbuRate], 'nbu', collect(), $nbuBank, $currencyRateService);
            if (!$rateExists) {
                $this->storeCurrencyRate($currency, ['cash' => $nbuRate], 'nbu', collect(), $nbuBank, $currencyRateService);
            }
        }
    }


    /**
     * Check if the rate exists.
     *
     * @param Currency $currency
     * @param array $rate
     * @param string $bankCode
     * @param Collection $banks
     * @param Bank $nbuBank
     * @param CurrencyRateService $currencyRateService
     *
     * @return bool
     */
    private function rateExists(
        Currency   $currency,
        array      $rate,
        string     $bankCode,
        Collection $banks,
        Bank       $nbuBank,
        CurrencyRateService $currencyRateService,
    ): bool
    {
        return $currencyRateService->query()
            ->where('currency_id', $currency->id)
            ->where('date', Carbon::parse(Arr::has($rate, 'cash.date')? Arr::get($rate, 'cash.date') : Arr::get($rate, 'card.date')))
            ->when($bankCode !== 'nbu', fn($query) => $query->where('bank_id', $banks->firstWhere('code', $bankCode)?->id))
            ->when($bankCode === 'nbu', fn($query) => $query->where('bank_id', $nbuBank?->id))
            ->exists();
    }

    /**
     * @param Currency $currency
     * @param array $rate
     * @param string $bankCode
     * @param Collection $banks
     * @param Bank $nbuBank
     * @param CurrencyRateService $currencyRateService
     *
     * @return void
     */
    private function storeCurrencyRate(
        Currency $currency,
        array    $rate,
        string   $bankCode,
        Collection    $banks,
        Bank     $nbuBank,
        CurrencyRateService $currencyRateService,
    ): void
    {
        $cashData = Arr::get($rate, 'cash');
        $cardData = Arr::get($rate, 'card');
        $currencyRateData = [
            'currency_id' => $currency->id,
            'bid' => null,
            'ask' => null,
            'date' => null,
        ];
        if ($bankCode !== 'nbu') {
            $currencyRateData['bank_id'] = $banks->firstWhere('code', $bankCode)?->id;
        } else {
            $currencyRateData['bank_id'] = optional($nbuBank)->id;
        }
        if (!empty($cashData)) {
            $currencyRateData['bid'] = Arr::get($cashData, 'bid');
            $currencyRateData['ask'] = Arr::get($cashData, 'ask');
            $currencyRateData['date'] = Carbon::parse(Arr::get($cashData, 'date'));
        } elseif (!empty($cardData)) {
            $currencyRateData['bid'] = Arr::get($cardData, 'bid');
            $currencyRateData['ask'] = Arr::get($cardData, 'ask');
            $currencyRateData['date'] = Carbon::parse(Arr::get($cardData, 'date'));
        }
        if ($currencyRateData['bid'] !== null && $currencyRateData['ask'] !== null && $currencyRateData['date'] !== null) {
            $currencyRateService->store($currencyRateData);
        }
    }

}
