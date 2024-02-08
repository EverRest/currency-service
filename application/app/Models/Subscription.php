<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'bank_id',
        'enable_notifications',
    ];

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function banks(): BelongsToMany
    {
        return $this->belongsToMany(Bank::class, 'subscription_bank', 'subscription_id', 'bank_id');
    }

    /**
     * @return BelongsToMany
     */
    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'subscription_currency', 'subscription_id', 'currency_id');
    }
}
