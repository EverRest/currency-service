<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\HasBankRelation;
use App\Traits\HasCurrencyRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurrencyRate extends Model
{
    use HasFactory;
    use HasBankRelation;
    use HasCurrencyRelation;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'bank_id',
        'currency_id',
        'bid',
        'ask',
        'date',
    ];

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'bid' => 'float',
        'ask' => 'float',
    ];

    /**
     * @return HasMany
     */
    public function criticalRateChangeHistories(): HasMany
    {
        return $this->hasMany(CriticalRateChangeHistory::class, 'previous_currency_rate_id');
    }
}
