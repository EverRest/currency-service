<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Services\Eloquent\ExchangeRateService;
use App\Services\Http\MinFinService;
use App\Services\Http\NbuService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param ExchangeRateService $exchangeRateService
     * @param MinFinService $minFinService
     * @param NbuService $nbuService
     *
     * @return void
     * @throws Exception
     */
    public function handle(
        ExchangeRateService $exchangeRateService,
        MinFinService       $minFinService,
        NbuService          $nbuService
    ): void
    {
        $nbuDtoArray = $nbuService->getExchangeRatesDtoArray();
        $minFinDtoArray = $minFinService->getExchangeRatesDtoArray();
        foreach (array_merge($nbuDtoArray, $minFinDtoArray) as $dto) {
            $exchangeRateService->createFromDto($dto);
        }
    }
}
