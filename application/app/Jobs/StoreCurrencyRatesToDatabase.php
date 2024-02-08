<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreCurrencyRatesToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //        $currencies = $currencyService->query()->get();
//        $currencies->each(
//            fn($currency) => $minFinService->getExchangeRates($currency)->each(
//                function ($currency) use ($currencyService) {
//                $currencyRates = $financeUaService->getExchangeRates($currency['slug']);
//                $currencyService->storeCurrencyRates($currencyRates);
//            }
//            );
//        );
//        $minFinService->getExchangeRates($currencyService->code)->each(function ($currency) use ($financeUaService, $currencyService) {
//            $currencyRates = $financeUaService->getExchangeRates($currency['slug']);
//            $currencyService->storeCurrencyRates($currencyRates);
//        });
    }
}
