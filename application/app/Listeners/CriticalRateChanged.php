<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExchangeRateChanged;
use App\Services\Eloquent\CriticalRateChangeHistoryService;
use App\Services\Eloquent\ExchangeRateService;
use App\Services\Eloquent\UserService;
use App\Traits\HasIsMailEnabled;

class CriticalRateChanged
{
    use HasIsMailEnabled;

    /**
     * @param ExchangeRateService $ExchangeRateService
     * @param UserService $userService
     * @param CriticalRateChangeHistoryService $criticalRateChangeHistoryService
     */
    public function __construct(
        private readonly ExchangeRateService              $ExchangeRateService,
        private readonly UserService                      $userService,
        private readonly CriticalRateChangeHistoryService $criticalRateChangeHistoryService,
    )
    {
    }

    /**
     * @param ExchangeRateChanged $event
     *
     * @return void
     */
    public function handle(ExchangeRateChanged $event): void
    {
        if (!$this->getIsMailEnabled()) {
            return;
        }
        $newExchangeRate = $event->ExchangeRate;
        if ($this->ExchangeRateService->checkForCriticalChange($newExchangeRate)) {
            $notifiers = $this->userService->getUsersWithEnabledAlert();
            $previousExchangeRate = $this->ExchangeRateService->getPreviousExchangeRate($newExchangeRate);
            $this->ExchangeRateService->notifyCriticalRateChange($notifiers, $previousExchangeRate, $newExchangeRate);
            $this->criticalRateChangeHistoryService->firstOrCreate([
                'previous_currency_rate_id' => $previousExchangeRate->id,
                'new_currency_rate_id' => $newExchangeRate->id,
            ]);
        }
    }
}
