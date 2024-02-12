<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExchangeRateChanged;
use App\Services\App\NotificationService;
use App\Services\Eloquent\CriticalRateChangeHistoryService;
use App\Services\Eloquent\ExchangeRateService;
use App\Services\Eloquent\UserService;
use App\Traits\HasIsMailEnabled;

class CriticalRateChanged
{
    use HasIsMailEnabled;

    /**
     * @param ExchangeRateService $exchangeRateService
     * @param UserService $userService
     * @param CriticalRateChangeHistoryService $criticalRateChangeHistoryService
     * @param NotificationService $notificationService
     */
    public function __construct(
        private readonly ExchangeRateService              $exchangeRateService,
        private readonly UserService                      $userService,
        private readonly CriticalRateChangeHistoryService $criticalRateChangeHistoryService,
        private readonly NotificationService              $notificationService,
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
        $newExchangeRate = $event->exchangeRate;
        if ($this->exchangeRateService->checkForCriticalChange($newExchangeRate)) {
            $notifiers = $this->userService->getUsersWithEnabledAlert();
            $previousExchangeRate = $this->exchangeRateService->getPreviousExchangeRate($newExchangeRate);
            $this->notificationService->notifyCriticalRateChange($notifiers, $previousExchangeRate, $newExchangeRate);
            $this->criticalRateChangeHistoryService->firstOrCreate([
                'previous_currency_rate_id' => $previousExchangeRate->id,
                'new_currency_rate_id' => $newExchangeRate->id,
            ]);
        }
    }
}
