<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\HasBankRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankBranch extends Model
{
    use HasFactory;
    use HasBankRelation;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'bank_id',
        'address',
        'lat',
        'lng',
        'phone_number',
        'department_name',
    ];

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
