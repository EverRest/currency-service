<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Currency;
use App\Services\Super\ServiceWithEloquentModel;

class CurrencyService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = Currency::class;
}
