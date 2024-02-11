<?php
declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class NbuExchangeRate extends Data
{
    /**
     * @param string $bank_id
     * @param string $currency
     * @param float $bid
     * @param float $ask
     * @param string $date
     */
    public function __construct(
        #[MapInputName('cc')]
        public string $currency,
        #[MapInputName('rate')]
        public float $bid,
        #[MapInputName('rate')]
        public float $ask,
        #[MapInputName('exchangedate')]
        public string $date
    ) {
    }
}
