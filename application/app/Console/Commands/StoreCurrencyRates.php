<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\StoreCurrencyRatesToDatabase;
use Illuminate\Console\Command;

class StoreCurrencyRates extends Command
{
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
    protected $description = 'Store CurrencyRates from MinFinService';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        StoreCurrencyRatesToDatabase::dispatch();
    }
}
