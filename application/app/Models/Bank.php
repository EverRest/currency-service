<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\HasExchangeRatesRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory;
    use HasExchangeRatesRelation;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'logo',
        'website',
        'phone_number',
        'email',
        'address',
        'rating',
    ];

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'external_id',
    ];

    /**
     * @var string[] $with
     */
    protected $with = ['ExchangeRates', 'ExchangeRates.currency', 'bankBranches'];

    /**
     * @return HasMany
     */
    public function bankBranches(): HasMany
    {
        return $this->hasMany(BankBranch::class);
    }
}
