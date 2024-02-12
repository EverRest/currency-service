<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ExchangeRateProviderEnum;
use App\Services\Eloquent\ExchangeRateService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class StoreExchangeRatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string $rateProvider
     */
    public function __construct(private readonly string $rateProvider)
    {
    }

    /**
     * @param ExchangeRateService $exchangeRateService
     *
     * @return int
     * @throws Exception
     */
    public function handle(ExchangeRateService $exchangeRateService,): int
    {
        $service = App::make(ExchangeRateProviderEnum::getHttpServicePath($this->rateProvider));
        $dtoArray = $service->getExchangeRatesDtoArray();
        foreach ($dtoArray as $dto) {
            $exchangeRateService->storeFromDto($dto);
        }

        return 1;
    }
}
