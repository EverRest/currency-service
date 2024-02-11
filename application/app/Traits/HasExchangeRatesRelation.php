<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasExchangeRatesRelation
{
    /**
     * @param $related
     * @param $foreignKey
     * @param $localKey
     *
     * @return mixed
     */
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);

    /**
     * @return HasMany
     */
    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class);
    }
}
