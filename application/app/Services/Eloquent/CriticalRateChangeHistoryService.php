<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\CriticalRateChangeHistory;
use App\Services\Super\ServiceWithEloquentModel;

class CriticalRateChangeHistoryService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = CriticalRateChangeHistory::class;
}
