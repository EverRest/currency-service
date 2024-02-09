<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\User;
use App\Services\Abstracts\ServiceWithEloquentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = User::class;

    /**
     * @param User $user
     *
     * @return Model
     */
    public function toggleCriticalRateChangeSubscription(User $user): Model
    {
        return $this->patch($user, 'is_alert_enabled', !$user->is_alert_enabled);
    }

    /**
     * @return Collection
     */
    public function getUsersWithEnabledAlert(): Collection
    {
        return $this->query()->where('is_alert_enabled', true)->get();
    }

    /**
     * @param int $bankId
     * @param int $currencyId
     *
     * @return Collection
     */
    public function getUsersWithSubscription(int $bankId, int $currencyId): Collection
    {
        return $this->query()
            ->whereHas('subscriptions', function ($query) use ($bankId, $currencyId) {
                $query->whereHas('banks',
                    fn($bankQuery) => $bankQuery->where('banks.id', $bankId)
                )->whereHas('currencies', fn($currencyQuery) => $currencyQuery->where('currencies.id', $currencyId)
                );
            })->get();
    }
}
