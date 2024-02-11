<?php
declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Services\Eloquent\BankService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class MinFinExchangeRate extends Data
{
    /**
     * @param int $bank_id
     */
    public int $bank_id;

    /**
     * @var string|mixed $bid
     */
    public float $bid;

    /**
     * @var float|mixed $ask
     */
    public float $ask;

    /**
     * @var Carbon $date
     */
    public Carbon $date;

    /**
     * @var int $currency_id
     */
    public int $currency_id;

    /**
     * @param array $data
     * @param int $currencyId
     * @param BankService $bankService
     */
    public function __construct(array $data, int $currencyId, private readonly BankService $bankService)
    {
        $this->setBankId($data);
        $this->setCurrencyId($currencyId);
        $this->setBid($data);
        $this->setAsk($data);
        $this->setDate($data);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    private function setBankId(array $data): void
    {
        $bank = $this->bankService
            ->findByCode(
                Arr::get($data, 'slug')
            );
        $this->bank_id = $bank->id;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    private function setBid(array $data): void
    {
        $bid = floatval(
            Arr::has($data, 'cash.bid') ? Arr::get($data, 'cash.bid') :
                Arr::get($data, 'card.bid')
        );
        $this->bid = $bid;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    private function setAsk(array $data): void
    {
        $ask = floatval(
            Arr::has($data, 'cash.ask') ? Arr::get($data, 'cash.ask') :
                Arr::get($data, 'card.ask')
        );
        $this->ask = $ask;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    private function setDate(array $data): void
    {
        $this->date = Carbon::parse(
            Arr::has($data, 'cash.date') ? Arr::get($data, 'cash.date') :
                Arr::get($data, 'card.date')
        );
    }

    /**
     * @param int $currencyId
     *
     * @return void
     */
    private function setCurrencyId(int $currencyId): void
    {
        $this->currency_id = $currencyId;
    }
}
