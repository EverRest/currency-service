<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\HasBankRelation;
use App\Traits\HasCurrencyRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;
    use HasBankRelation;
    use HasCurrencyRelation;

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
}
