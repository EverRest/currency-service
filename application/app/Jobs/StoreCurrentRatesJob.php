<?php
declare(strict_types=1);

namespace App\Jobs;

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

class StoreCurrentRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(
        BankService         $bankService,
        CurrencyService     $currencyService,
        CurrencyRateService $currencyRateService,
        MinFinService       $minFinService,
        NbuService          $nbuService,
    ): void
    {
        $banks = $bankService->query()->get();
        $bankCodes = $banks->pluck('code')->toArray();
        $currencyService->query()->each(
            function ($currency) use ($banks, $bankCodes, $nbuService, $minFinService, $currencyService, $currencyRateService) {
                $allCurrentCurrencyRates = $minFinService->getAllExchangeRates($currency->code);
                $appCurrencyRates = $allCurrentCurrencyRates->filter(
                    fn($rate) => in_array(Arr::get($rate, 'slug'), $bankCodes, true)
                );
                $appCurrencyRates->each(
                    function ($rate) use ($currencyRateService, $currency, $banks) {
                        $rateExists = $currencyRateService->query()
                            ->where('bank_id', $banks->firstWhere('code', Arr::get($rate, 'slug'))?->id)
                            ->where('currency_id', $currency->id)
                            ->where('date', Carbon::parse(Arr::get($rate, 'card.date')))
                            ->exists();
                        if ($rateExists) {
                            return;
                        }
                        $currencyRateService->store(
                            [
                                'currency_id' => $currency->id,
                                'bank_id' => $banks->firstWhere('code', Arr::get($rate, 'slug'))?->id,
                                'bid' => Arr::get($rate, 'card.bid'),
                                'ask' => Arr::get($rate, 'card.ask'),
                                'date' => Carbon::parse(Arr::get($rate, 'card.date'))
                            ]
                        );
                    }
                );
                $nbuCurrencyRates = $nbuService->getExchangeRates();
                $nbuBank = $banks->firstWhere('code', 'nbu');
                $nbuCurrencyRates->each(
                    function ($rate) use ($currencyRateService, $currency, $nbuBank) {
                        $rateExists = $currencyRateService->query()
                            ->where('bank_id', $nbuBank?->id)
                            ->where('currency_id', $currency->id)
                            ->where('date', Carbon::parse(Arr::get($rate, 'exchangedate')))
                            ->exists();
                        if ($rateExists) {
                            return;
                        }
                        $currencyRateService->store(
                            [
                                'currency_id' => $currency->id,
                                'bank_id' => $nbuBank?->id,
                                'bid' => Arr::get($rate, 'rate'),
                                'ask' => Arr::get($rate, 'rate'),
                                'date' => Carbon::parse(Arr::get($rate, 'exchangedate'))
                            ]
                        );
                    }
                );
            }
        );
    }
}
