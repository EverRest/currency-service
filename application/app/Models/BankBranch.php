<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    use HasFactory;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'bank_id', 'address', 'lat', 'lng', 'phone_number', 'department_name',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
