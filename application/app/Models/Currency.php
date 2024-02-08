<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCurrencyRatesRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    use HasCurrencyRatesRelation;

    /**
     * @var string[] $fillable
     */
    protected $fillable = ['code', 'name'];

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
