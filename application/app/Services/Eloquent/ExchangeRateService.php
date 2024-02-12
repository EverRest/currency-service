<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\DataTransferObjects\MinFinExchangeRate;
use App\DataTransferObjects\NbuExchangeRate;
use App\Models\Bank;
use App\Models\ExchangeRate;
use App\Services\Super\ServiceWithEloquentModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class ExchangeRateService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = ExchangeRate::class;

    /**
     * @var Bank $nbuBank
     */
    private Bank $nbuBank;

    /**
     * @param BankService $bankService
     * @param CurrencyService $currencyService
     */
    public function __construct(
        private readonly BankService     $bankService,
        private readonly CurrencyService $currencyService,
    )
    {
        $this->currencies = $this->currencyService->list();
        $this->nbuBank = $this->bankService->getNbuBank();
    }

    /**
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param array $bankIds
     * @param array $currencyIds
     *
     * @return Collection
     */
    public function getStatisticByPeriod(mixed $fromDate, mixed $toDate, array $bankIds, array $currencyIds): Collection
    {
        return $this->query()
            ->whereBetween('date', [$fromDate, $toDate])
            ->when($bankIds, function ($query) use ($bankIds) {
                return $query->whereIn('bank_id', $bankIds);
            })
            ->when($currencyIds, function ($query) use ($currencyIds) {
                return $query->whereIn('currency_id', $currencyIds);
            })
            ->get();
    }

    /**
     * Get the previous currency rate.
     *
     * @param ExchangeRate $newExchangeRate
     *
     * @return Model|ExchangeRate|null
     */
    public function getPreviousExchangeRate(ExchangeRate $newExchangeRate): null|Model|ExchangeRate
    {
        return $this->query()
            ->where('currency_id', $newExchangeRate->currency_id)
            ->where('bank_id', $newExchangeRate->bank_id)
            ->whereNot('id', $newExchangeRate->id)
            ->orderByDesc('date')
            ->first();
    }

    /**
     * @return Collection
     */
    public function getAvgRates(): Collection
    {
        $avgRates = $this->query()->averageRate()->get();
        $nbuRates = $this->query()->nbuRateForCurrencies($this->bankService->query()->get()->pluck('id')->toArray());

        $result = [
            'avg' => $this->mergeRatesWithDetails($avgRates),
            'nbu' => $this->mergeRatesWithDetails($nbuRates),
        ];

        return Collection::make($result);
    }

    /**
     * @param Data $dto
     *
     * @return Model
     * @throws Exception
     */
    public function storeFromDto(Data $dto): Model
    {
        if ($dto instanceof NbuExchangeRate) {
            return $this->storeNbuExchangeRate($dto);
        }
        if ($dto instanceof MinFinExchangeRate) {
            return $this->storeMinFinExchangeRate($dto);
        }

        throw new Exception('Unknown DTO');
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
     * @param NbuExchangeRate $dto
     *
     * @return Model
     */
    private function storeNbuExchangeRate(NbuExchangeRate $dto): Model
    {
        $attributes = [
            ...$dto->except('currency')->toArray(),
            'currency_id' => $this->currencyService->findByCode($dto->currency)->id,
            'bank_id' => $this->nbuBank->id,
        ];

        return $this->firstOrCreate(
            $attributes
        );
    }

    /**
     * @param MinFinExchangeRate $dto
     *
     * @return Model
     */
    private function storeMinFinExchangeRate(MinFinExchangeRate $dto): Model
    {
        return $this->firstOrCreate($dto->toArray());
    }

    /**
     * Merge rates with additional details (bank name and currency code).
     *
     * @param Collection $rates
     * @return Collection
     */
    private function mergeRatesWithDetails(Collection $rates): Collection
    {
        return $rates->map(function ($rate) {
            $currency = $this->currencyService->findOrFail($rate->currency_id);

            return [
                'currency_id' => $rate->currency_id,
                'bid' => $rate->bid,
                'ask' => $rate->ask,
                'currency_code' => $currency->code,
            ];
        });
    }
}
