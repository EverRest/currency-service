<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCurrencyRelation
{
    /**
     * @param $related
     * @param $foreignKey
     * @param $ownerKey
     * @param $relation
     *
     * @return BelongsTo
     */
    abstract public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null);

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
