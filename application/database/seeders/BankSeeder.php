<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Eloquent\BankService;
use App\Services\Http\FinanceUaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class BankSeeder extends Seeder
{
    private const BANK_CONFIG = 'banks';

    /**
     * @param BankService $bankService
     * @param FinanceUaService $financeUaService
     */
    public function __construct(private readonly BankService $bankService, private readonly FinanceUaService $financeUaService)
    {
    }

    /**
     * @return void
     */
    public function run()
    {
        if ($this->bankService->count() > 0) {
            return;
        }
        $banks = $this->financeUaService->getBankList();
        $banks->each(
            function ($bank) {
                $bankCode = Arr::get($bank, 'slug');
                if (in_array($bankCode, Config::get(self::BANK_CONFIG))) {
                    $this->bankService
                        ->firstOrCreate([
                            'name' => Arr::get($bank, 'title'),
                            'code' => $bankCode,
                            'description' => Arr::get($bank, 'longTitle'),
                            'logo' => Arr::get($bank, 'logo')[0],
                            'website' => Arr::get($bank, 'site'),
                            'phone_number' => Arr::get($bank, 'phone'),
                            'email' => Arr::get($bank, 'email'),
                            'address' => Arr::get($bank, 'legalAddress'),
                            'rating' => Arr::get($bank, 'ratingBank'),
                        ]);
                }
            }
        );
    }
}
