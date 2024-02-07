<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use Illuminate\Support\Facades\Config;

class CurrencySeeder extends Seeder
{
    private const CURRENCIES_CONFIG = 'currencies';

    /**
     * @return void
     */
    public function run()
    {
        foreach (Config::get(self::CURRENCIES_CONFIG) as $code => $name) {
            Currency::firstOrCreate([
                'name' => $name,
                'code' => $code
            ]);
        }
    }
}
