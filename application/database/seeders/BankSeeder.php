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
                if (Arr::has(Config::get(self::BANK_CONFIG), $bankCode)) {
                    $this->bankService
                        ->firstOrCreate([
                            'name' => Arr::get($bank, 'name'),
                            'description' => Arr::get($bank, 'description'),
                            'logo' => Arr::get($bank, 'logo'),
                            'website' => Arr::get($bank, 'website'),
                            'phone_number' => Arr::get($bank, 'phone'),
                            'email' => Arr::get($bank, 'email'),
                            'address' => Arr::get($bank, 'legal_address'),
                            'rating' => Arr::get($bank, 'rating'),
                        ]);
                }
            }
        );
    }
}
