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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly BankService         $bankService,
        private readonly CurrencyService     $currencyService,
        private readonly CurrencyRateService $currencyRateService,
        private readonly MinFinService       $minFinService,
        private readonly NbuService          $nbuService
    )
    {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $banks = $this->bankService->list();
        $bankCodes = $banks->pluck('code')->toArray();
        $this->currencyService->query()
            ->each(
                fn($currency) => $this->storeCurrencyRates($currency, $banks->to, $bankCodes)
            );
    }

    /**
     * @param Currency $currency
     * @param Collection $banks
     * @param array $bankCodes
     *
     * @return void
     */
    private function storeCurrencyRates(Currency $currency, Collection $banks, array $bankCodes): void
    {
        $allCurrentCurrencyRates = $this->minFinService
            ->getAllExchangeRates($currency->code);
        $nbuBank = $banks->firstWhere('code', 'nbu');
        $desiredBankCodes = Config::get('banks');
        $allCurrentCurrencyRates->each(
            function ($rate) use ($currency, $banks, $bankCodes, $nbuBank, $desiredBankCodes) {
                $bankCode = Arr::get($rate, 'slug');
                if (in_array($bankCode, $desiredBankCodes, true)) {
                    $rateExists = $this->rateExists($currency, $rate, $bankCode, $banks, $nbuBank);
                    if (!$rateExists) {
                        $this->storeCurrencyRate($currency, $rate, $bankCode, $banks, $nbuBank);
                    }
                }
            }
        );
    }

    /**
     * Check if the rate exists.
     *
     * @param Currency $currency
     * @param array $rate
     * @param string $bankCode
     * @param Collection $banks
     * @param Bank $nbuBank
     *
     * @return bool
     */
    private function rateExists(
        Currency   $currency,
        array      $rate,
        string     $bankCode,
        Collection $banks,
        Bank       $nbuBank): bool
    {
        return $this->currencyRateService->query()
            ->where('currency_id', $currency->id)
            ->where('date', Carbon::parse(Arr::get($rate, 'card.date')))
            ->when($bankCode !== 'nbu', fn($query) => $query->where('bank_id', $banks->firstWhere('code', $bankCode)?->id))
            ->when($bankCode === 'nbu', fn($query) => $query->where('bank_id', $nbuBank?->id))
            ->exists();
    }

    /**
     * @param Currency $currency
     * @param array $rate
     * @param string $bankCode
     * @param array $banks
     * @param Bank $nbuBank
     *
     * @return void
     */
    private function storeCurrencyRate(
        Currency $currency,
        array    $rate,
        string   $bankCode,
        Collection    $banks,
        Bank     $nbuBank
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
            $this->currencyRateService->store($currencyRateData);
        }
    }

}
