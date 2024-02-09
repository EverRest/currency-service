<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\CurrencyRate;
use App\Notifications\CriticalRateChangedNotification;
use App\Services\Abstracts\ServiceWithEloquentModel;
use App\Traits\HasGetRateChangeInPercents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CurrencyRateService extends ServiceWithEloquentModel
{
    use HasGetRateChangeInPercents;

    private const CRITICAL_RATE_CHANGE_IN_PERCENTS = 5.0;

    /**
     * @var string $model
     */
    protected string $model = CurrencyRate::class;

    public function __construct(private readonly BankService $bankService, private readonly CurrencyService $currencyService)
    {
    }

    /**
     * Check if the rate change is critical.
     *
     * @param CurrencyRate $newCurrencyRate
     *
     * @return bool
     */
    public function checkForCriticalChange(CurrencyRate $newCurrencyRate): bool
    {
        /**
         * @var CurrencyRate $previousCurrencyRate
         */
        $previousCurrencyRate = $this->getPreviousCurrencyRate($newCurrencyRate);
        if (!$previousCurrencyRate) {
            return false;
        }

        return $this->isRateChangeCritical($previousCurrencyRate, $newCurrencyRate);
    }

    /**
     * @param Collection $notifiers
     * @param $previousCurrencyRate
     * @param $newCurrencyRate
     *
     * @return void
     */
    public function notifyCriticalRateChange(Collection $notifiers, $previousCurrencyRate, $newCurrencyRate): void
    {
        $notifiers->each(
            fn($notifier) => $notifier->notify(
                new CriticalRateChangedNotification($previousCurrencyRate, $newCurrencyRate)
            )
        );
    }

    /**
     * Get the previous currency rate.
     *
     * @param CurrencyRate $newCurrencyRate
     *
     * @return ?Model
     */
    public function getPreviousCurrencyRate(CurrencyRate $newCurrencyRate): ?Model
    {
        return $this->query()
            ->where('currency_id', $newCurrencyRate->currency_id)
            ->where('bank_id', $newCurrencyRate->bank_id)
            ->whereNot('id', $newCurrencyRate->id)
            ->orderByDesc('date')
            ->first();
    }

    /**
     * @return Collection
     */
    public function getAvgRates(): Collection
    {
        return Collection::make([
            'avg' => $this->query()->averageRate(),
            'nbu' => $this->query()->nbuRateForCurrencies($this->bankService->query()->get()->pluck('id')->toArray()),
        ]);
    }

    /**
     * @param $query
     * @param array $filter
     *
     * @return Builder
     */
    protected function filter($query, array $filter): Builder
    {
        Arr::has($filter, 'bank_id')
            ? $query->byBanks(Arr::get($filter, 'bank_id'))
            : $query->byBanks($this->bankService->query()->get()->pluck('id')->toArray());

        Arr::has($filter, 'currency_id')
            ? $query->byCurrencies(Arr::get($filter, 'currency_id'))
            : $query->byCurrencies($this->currencyService->query()->get()->pluck('id')->toArray());
        $query->latestDate();

        return parent::filter($query, Arr::except($filter, ['bank_id', 'currency_id']));
    }

    /**
     * Check if the rate change is critical.
     *
     * @param CurrencyRate $previousCurrencyRate
     * @param CurrencyRate $newCurrencyRate
     *
     * @return bool
     */
    private function isRateChangeCritical(CurrencyRate $previousCurrencyRate, CurrencyRate $newCurrencyRate): bool
    {
        $askRateChangeInPercents = $this->getRateChangeInPercents(
            $previousCurrencyRate->ask,
            $newCurrencyRate->ask
        );

        $bidRateChangeInPercents = $this->getRateChangeInPercents(
            $previousCurrencyRate->bid,
            $newCurrencyRate->bid
        );

        return $askRateChangeInPercents > self::CRITICAL_RATE_CHANGE_IN_PERCENTS ||
            $bidRateChangeInPercents > self::CRITICAL_RATE_CHANGE_IN_PERCENTS;
    }
}
