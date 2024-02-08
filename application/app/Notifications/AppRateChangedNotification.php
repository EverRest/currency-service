<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Models\CurrencyRate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppRateChangedNotification extends Notification
{
    use Queueable;

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
        protected readonly string       $bank,
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
            ->line("Bank: $this->bank")
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'previous_rate' => $this->previousRate->toArray(),
            'new_rate' => $this->newRate->toArray(),
            'bank' => $this->bank,
        ];
    }

    /**
     * Get the rate change in percents.
     *
     * @param float $previousRate
     * @param float $newRate
     *
     * @return float
     */
    protected function getRateChangeInPercents(float $previousRate, float $newRate): float
    {
        return $previousRate ? (($newRate - $previousRate) / $previousRate) * 100 : 0;
    }
}
