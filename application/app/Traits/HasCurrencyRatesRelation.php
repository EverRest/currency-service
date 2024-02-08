<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\CurrencyRate;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCurrencyRatesRelation
{
    /**
     * @return HasMany
     */
    public function currencyRates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class);
    }
}
