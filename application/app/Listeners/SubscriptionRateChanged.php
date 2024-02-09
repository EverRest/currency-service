<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\CurrencyRateChanged;
use App\Services\Eloquent\CurrencyRateService;
use App\Services\Eloquent\UserService;
use App\Traits\HasIsMailEnabled;

class SubscriptionRateChanged
{
    use HasIsMailEnabled;

    /**
     * @param UserService $userService
     * @param CurrencyRateService $currencyRateService
     */
    public function __construct(
        private readonly UserService         $userService,
        private readonly CurrencyRateService $currencyRateService,
    )
    {
    }

    /**
     * @param CurrencyRateChanged $event
     *
     * @return void
     */
    public function handle(CurrencyRateChanged $event): void
    {
        if (!$this->getIsMailEnabled()) {
            return;
        }
        $newCurrencyRate = $event->currencyRate;
        $notifiers = $this->userService->getUsersWithEnabledAlert();
        $previousCurrencyRate = $this->currencyRateService->getPreviousCurrencyRate($newCurrencyRate);
        $this->currencyRateService->notifyCriticalRateChange($notifiers, $previousCurrencyRate, $newCurrencyRate);
    }
}
