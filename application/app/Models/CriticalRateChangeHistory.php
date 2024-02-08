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
        'previous_currency_rate_id',
        'new_currency_rate_id',
    ];

    /**
     * @return BelongsTo
     */
    public function previousCurrencyRate(): BelongsTo
    {
        return $this->belongsTo(CurrencyRate::class, 'previous_currency_rate_id');
    }

    /**
     * @return BelongsTo
     */
    public function newCurrencyRate(): BelongsTo
    {
        return $this->belongsTo(CurrencyRate::class, 'new_currency_rate_id');
    }
}

