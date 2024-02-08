<?php
declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\StoreCurrencyRates;
use App\Console\Commands\UpdateBankBranches;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(UpdateBankBranches::class)->dailyAt('00:01');;
        $schedule->command(StoreCurrencyRates::class)->hourly()->between('9:00', '18:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
