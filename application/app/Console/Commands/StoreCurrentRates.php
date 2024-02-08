<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\StoreCurrentRatesJob;
use Illuminate\Console\Command;

class StoreCurrentRates extends Command
{
    private const SUCCESS_MESSAGE = 'Currency rates stored successfully';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:store-currency-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store CurrencyRates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        StoreCurrentRatesJob::dispatch();
        $this->info(self::SUCCESS_MESSAGE);

        return 1;
    }
}
