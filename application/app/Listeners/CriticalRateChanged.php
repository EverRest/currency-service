<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\CurrencyRateChanged;
use App\Services\Eloquent\CriticalRateChangeHistoryService;
use App\Services\Eloquent\CurrencyRateService;
use App\Services\Eloquent\UserService;
use App\Traits\HasIsMailEnabled;

class CriticalRateChanged
{
    use HasIsMailEnabled;

    /**
     * @param CurrencyRateService $currencyRateService
     * @param UserService $userService
     * @param CriticalRateChangeHistoryService $criticalRateChangeHistoryService
     */
    public function __construct(
        private readonly CurrencyRateService              $currencyRateService,
        private readonly UserService                      $userService,
        private readonly CriticalRateChangeHistoryService $criticalRateChangeHistoryService,
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
        if ($this->currencyRateService->checkForCriticalChange($newCurrencyRate)) {
            $notifiers = $this->userService->getUsersWithEnabledAlert();
            $previousCurrencyRate = $this->currencyRateService->getPreviousCurrencyRate($newCurrencyRate);
            $this->currencyRateService->notifyCriticalRateChange($notifiers, $previousCurrencyRate, $newCurrencyRate);
            $this->criticalRateChangeHistoryService->firstOrCreate([
                'previous_currency_rate_id' => $previousCurrencyRate->id,
                'new_currency_rate_id' => $newCurrencyRate->id,
            ]);
        }
    }
}
