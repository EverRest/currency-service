<?php
declare(strict_types=1);

namespace App\Providers;

use App\Events\CurrencyRateChanged;
use App\Listeners\CriticalRateChanged;
use App\Listeners\SubscriptionRateChanged;
use App\Models\CurrencyRate;
use App\Notifications\CriticalRateChangedNotification;
use App\Observers\CurrencyRateObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CurrencyRateChanged::class => [
            SubscriptionRateChanged::class,
            CriticalRateChanged::class,
        ],
    ];

    /**
     * @return void
     */
    public function boot(): void
    {
        CurrencyRate::observe(CurrencyRateObserver::class);
    }

    /**
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
