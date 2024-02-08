<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasBankRelation
{
    /**
     * @return BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
