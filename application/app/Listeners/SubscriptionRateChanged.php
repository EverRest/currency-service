<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExchangeRateChanged;
use App\Services\Eloquent\ExchangeRateService;
use App\Services\Eloquent\UserService;
use App\Traits\HasIsMailEnabled;

class SubscriptionRateChanged
{
    use HasIsMailEnabled;

    /**
     * @param UserService $userService
     * @param ExchangeRateService $ExchangeRateService
     */
    public function __construct(
        private readonly UserService         $userService,
        private readonly ExchangeRateService $ExchangeRateService,
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
        $notifiers = $this->userService->getUsersWithEnabledAlert();
        $previousExchangeRate = $this->ExchangeRateService->getPreviousExchangeRate($newExchangeRate);
        $this->ExchangeRateService->notifyCriticalRateChange($notifiers, $previousExchangeRate, $newExchangeRate);
    }
}
