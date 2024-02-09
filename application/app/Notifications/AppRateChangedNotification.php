<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Models\CurrencyRate;
use App\Traits\HasGetRateChangeInPercents;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;


class AppRateChangedNotification extends Notification
{
    use Queueable;
    use HasGetRateChangeInPercents;

    /**
     * @var string $subject
     */
    protected string $subject = '';

    /**
     * @var string $message
     */
    protected string $message = '';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected readonly CurrencyRate $previousRate,
        protected readonly CurrencyRate $newRate,
    )
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->line($this->message)
            ->line("Bank: {$this->previousRate->bank->name}")
            ->line('Buy: ')
            ->line('New Rate: ' . $this->newRate->ask ?? '')
            ->line('Previous Rate: ' . $this->previousRate->ask ?? '')
            ->line('Change Percentage: ' . $this->getRateChangeInPercents($this->previousRate->ask, $this->newRate->ask) . '%')
            ->line('Sell: ')
            ->line('New Rate: ' . $this->newRate->bid ?? '')
            ->line('Previous Rate: ' . $this->previousRate->bid ?? '')
            ->line('Change Percentage: ' . $this->getRateChangeInPercents($this->previousRate->bid, $this->newRate->bid) . '%')
            ->line("Previous Date: {$this->previousRate->date}")
            ->line("Date Of Change: {$this->newRate->date}");

    }
}
