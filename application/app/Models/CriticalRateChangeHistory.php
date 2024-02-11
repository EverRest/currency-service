<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriticalRateChangeHistory extends Model
{
    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'previous_exchange_rate_id',
        'new_exchange_rate_id',
    ];

    /**
     * @return BelongsTo
     */
    public function previousExchangeRate(): BelongsTo
    {
        return $this->belongsTo(ExchangeRate::class, 'previous_exchange_rate_id');
    }

    /**
     * @return BelongsTo
     */
    public function newExchangeRate(): BelongsTo
    {
        return $this->belongsTo(ExchangeRate::class, 'new_exchange_rate_id');
    }
}

