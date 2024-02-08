<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\User;
use App\Services\Abstracts\ServiceWithEloquentModel;
use Illuminate\Database\Eloquent\Model;

class UserService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = User::class;

    public function toggleCriticalRateChangeSubscription(User $user): Model
    {
        return $this->patch($user, 'is_alert_enabled', !$user->is_alert_enabled);
    }
}
