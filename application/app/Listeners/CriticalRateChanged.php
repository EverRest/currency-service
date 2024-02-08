<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\CurrencyRateChanged;
use App\Services\Eloquent\CurrencyRateService;
use App\Services\Eloquent\UserService;

class CriticalRateChanged
{
    /**
     * @param CurrencyRateService $currencyRateService
     * @param UserService $userService
     */
    public function __construct(
        private readonly CurrencyRateService $currencyRateService,
        private readonly UserService         $userService,
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
        $notifiers = $this->userService->getUsersWithEnabledAlert();
        $newCurrencyRate = $event->currencyRate;
        if ($this->currencyRateService->checkForCriticalChange($newCurrencyRate)) {
            $previousCurrencyRate = $this->currencyRateService->getPreviousCurrencyRate($newCurrencyRate);
            $this->currencyRateService->notifyCriticalRateChange($notifiers, $previousCurrencyRate, $newCurrencyRate);

        }
    }
}
