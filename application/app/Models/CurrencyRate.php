<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\HasBankRelation;
use App\Traits\HasCurrencyRelation;
use Illuminate\Database\Eloquent\Builder;
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


    // Scopes

    /**
     * @param Builder $query
     * @param array $banks
     *
     * @return mixed
     */
    public function scopeByBanks(Builder $query, array $banks): mixed
    {
        return $query->whereIn('bank_id', $banks);
    }

    /**
     * @param Builder $query
     * @param array $currencies
     *
     * @return mixed
     */
    public function scopeByCurrencies(Builder $query, array $currencies): mixed
    {
        return $query->whereIn('currency_id', $currencies);
    }

    /**
     * @param Builder $query
     *
     * @return mixed
     */
    public function scopeLatestDate(Builder $query): mixed
    {
        return $query->latest('date');
    }

    /**
     * @param Builder $query
     *
     * @return mixed
     */
    public function scopeNbuRate(Builder $query): mixed
    {
        return $query->whereHas('bank', function (Builder $q) {
            $q->where('code', 'nbu');
        })->latestDate()->first();
    }

    /**
     * Scope to get the latest NBU rate for a list of currencies.
     *
     * @param Builder $query
     * @param array $currencyIds
     *
     * @return mixed
     */
    public function scopeNbuRateForCurrencies(Builder $query, array $currencyIds): mixed
    {
        return $query->whereIn('currency_id', $currencyIds)
            ->whereHas('bank', function (Builder $q) {
                $q->where('code', 'nbu');
            })->latestDate()->get();
    }

    /**
     * @param Builder $query
     *
     * @return mixed
     */
    public function scopeAverageRate(Builder $query): mixed
    {
        return $query->whereHas('bank', function (Builder $q) {
            $q->where('code', '!=', 'nbu');
        })
            ->selectRaw('COALESCE(AVG(bid), ?) as bid', [0.0])
            ->selectRaw('COALESCE(AVG(ask), ?) as ask', [0.0])
            ->first();
    }
}
