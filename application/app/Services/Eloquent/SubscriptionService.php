<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Subscription;
use App\Services\Abstracts\ServiceWithEloquentModel;

class SubscriptionService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = Subscription::class;
}
