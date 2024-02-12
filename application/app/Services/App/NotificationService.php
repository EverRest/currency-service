<?php
declare(strict_types=1);

namespace App\Services\App;

use App\Notifications\CriticalRateChangedNotification;
use App\Notifications\SubscribedRateChangedNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    private const CRITICAL_RATE_CHANGE_NOTIFICATION = CriticalRateChangedNotification::class;
    private const SUBSCRIPTION_RATE_CHANGE_NOTIFICATION = SubscribedRateChangedNotification::class;

    /**
     * @param Collection $notifiers
     * @param $previousExchangeRate
     * @param $newExchangeRate
     *
     * @return void
     */
    public function notifyCriticalRateChange(Collection $notifiers, $previousExchangeRate, $newExchangeRate): void
    {
        $this->notifyUsersAboutCurrencyRateChanges(
            self::CRITICAL_RATE_CHANGE_NOTIFICATION,
            $notifiers,
            $previousExchangeRate,
            $newExchangeRate
        );
    }

    /**
     * @param Collection $notifiers
     * @param $previousExchangeRate
     * @param $newExchangeRate
     *
     * @return void
     */
    public function notifySubscriberRateChange(Collection $notifiers, $previousExchangeRate, $newExchangeRate): void
    {
        $this->notifyUsersAboutCurrencyRateChanges(
            self::SUBSCRIPTION_RATE_CHANGE_NOTIFICATION,
            $notifiers,
            $previousExchangeRate,
            $newExchangeRate
        );
    }

    /**
     * @param string $notification
     * @param Collection $notifiers
     * @param $previousExchangeRate
     * @param $newExchangeRate
     *
     * @return void
     */
    private function notifyUsersAboutCurrencyRateChanges(string $notification, Collection $notifiers, $previousExchangeRate, $newExchangeRate): void
    {
        $notifiers->each(
            fn($notifier) => $notifier->notify(
                new $notification($previousExchangeRate, $newExchangeRate)
            )
        );
    }
}
