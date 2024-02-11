<?php
declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\StoreExchangeRates;
use App\Console\Commands\UpdateBankBranches;
use App\Enums\ExchangeRateProviderEnum;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(UpdateBankBranches::class)
            ->dailyAt('00:01');
        $schedule->command(
            StoreExchangeRates::class,
            [
                ExchangeRateProviderEnum::NBU
            ]
        )->dailyAt('16:00');
        $schedule->command(
            StoreExchangeRates::class,
            [
                ExchangeRateProviderEnum::MinFin
            ]
        )->between('8:00', '20:00')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
