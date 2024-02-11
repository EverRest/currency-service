<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Currency;
use App\Services\Super\ServiceWithEloquentModel;
use App\Traits\HasFindByCode;

class CurrencyService extends ServiceWithEloquentModel
{
    use HasFindByCode;

    /**
     * @var string $model
     */
    protected string $model = Currency::class;
}
