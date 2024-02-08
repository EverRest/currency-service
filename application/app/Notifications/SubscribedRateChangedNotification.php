<?php
declare(strict_types=1);

namespace App\Notifications;

class SubscribedRateChangedNotification extends AppRateChangedNotification
{
    /**
     * @var string $subject
     */
    protected string $subject = 'Exchange Rate Change Alert';

    /**
     * @var string $message
     */
    protected string $message = 'The exchange rate for your subscribed currency has changed.';
}
