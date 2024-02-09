<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasBankRelation
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
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
