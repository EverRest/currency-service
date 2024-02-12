<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\ExchangeRateChanged;
use App\Services\App\NotificationService;
use App\Services\Eloquent\ExchangeRateService;
use App\Services\Eloquent\UserService;
use App\Traits\HasIsMailEnabled;

class SubscriptionRateChanged
{
    use HasIsMailEnabled;

    /**
     * @param UserService $userService
     * @param ExchangeRateService $exchangeRateService
     * @param NotificationService $notificationService
     */
    public function __construct(
        private readonly UserService         $userService,
        private readonly ExchangeRateService $exchangeRateService,
        private readonly NotificationService $notificationService,
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
        $notifiers = $this->userService->getUsersWithEnabledAlert();
        $previousExchangeRate = $this->exchangeRateService->getPreviousExchangeRate($newExchangeRate);
        $this->notificationService->notifySubscriberRateChange($notifiers, $previousExchangeRate, $newExchangeRate);
    }
}
