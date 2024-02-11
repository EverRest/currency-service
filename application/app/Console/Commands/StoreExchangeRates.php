<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ExchangeRateProviderEnum;
use App\Jobs\StoreExchangeRatesJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class StoreExchangeRates extends Command
{
    private const SUCCESS_MESSAGE = 'Currency rates stored successfully';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:store-exchange-rates {provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store ExchangeRates';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): int
    {
        $provider = $this->argument('provider');
        if(!in_array($provider, ExchangeRateProviderEnum::toArray())) {
            throw new Exception("Invalid provider $provider.");
        }
        StoreExchangeRatesJob::dispatch($provider);
        $this->info(get_class($this) . ' : ' . Carbon::now()->toDateTimeString() . ' - ' . self::SUCCESS_MESSAGE);

        return 1;
    }
}
