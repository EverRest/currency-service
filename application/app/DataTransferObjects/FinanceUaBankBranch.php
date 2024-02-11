<?php
declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class FinanceUaBankBranch extends Data
{
    /**
     * @param string $bank_id
     * @param float $external_id
     * @param float $address
     * @param string $lat
     * @param string $lng
     * @param string $phone_number
     * @param string $department_name
     */
    public function __construct(
        #[MapInputName('bank_id')]
        public int $bank_id,
        #[MapInputName('external_id')]
        public string $external_id,
        #[MapInputName('address')]
        public string $address,
        #[MapInputName('lat')]
        public string $lat,
        #[MapInputName('lng')]
        public string $lng,
        #[MapInputName('phone')]
        public string $phone_number,
        #[MapInputName('branch_name')]
        public string $department_name,
    )
    {
    }
}
