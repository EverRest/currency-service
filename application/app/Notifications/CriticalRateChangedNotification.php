<?php
declare(strict_types=1);

namespace App\Notifications;

class CriticalRateChangedNotification extends AppRateChangedNotification
{
    /**
     * @var string $subject
     */
    protected string $subject = 'Critical Exchange Rate Change Alert';

    /**
     * @var string $message
     */
    protected string $message = 'The exchange rate for your subscribed currency has changed significantly.';
}
