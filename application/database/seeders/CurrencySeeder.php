<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Eloquent\CurrencyService;
use Illuminate\Database\Seeder;
use App\Models\Currency;
use Illuminate\Support\Facades\Config;

class CurrencySeeder extends Seeder
{
    private const CURRENCIES_CONFIG = 'currencies';

    /**
     * @param CurrencyService $currencyService
     */
    public function __construct(private CurrencyService $currencyService)
    {
    }

    /**
     * @return void
     */
    public function run()
    {
        foreach (Config::get(self::CURRENCIES_CONFIG) as $code => $name) {
            $this->currencyService
                ->firstOrCreate([
                    'name' => $name,
                    'code' => $code
                ]);
        }
    }
}
