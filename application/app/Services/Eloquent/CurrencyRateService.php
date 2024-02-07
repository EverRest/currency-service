<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\CurrencyRate;
use App\Services\Abstracts\ServiceWithEloquentModel;

class CurrencyRateService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = CurrencyRate::class;
}
