<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Subscription;
use App\Services\Super\ServiceWithEloquentModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubscriptionService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = Subscription::class;

    /**
     * @param array $data
     *
     * @return Model
     * @throws Exception
     */
    public function store(array $data): Model
    {
        try {
            DB::beginTransaction();
            $subscription = parent::store(Arr::only($data, 'user_id'));
            $subscription->currencies()->attach(Arr::get($data, 'currency_id'));
            $subscription->banks()->attach(Arr::get($data, 'bank_id'));
            DB::commit();

            return $subscription;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update and refresh model
     *
     * @param Model $model
     * @param array $data
     *
     * @return Model
     * @throws Exception
     */
    public function update(Model $model, array $data): Model
    {
        try {
            DB::beginTransaction();
            $model->currencies()->sync(Arr::get($data, 'currency_id'));
            $model->banks()->sync(Arr::get($data, 'bank_id'));
            DB::commit();

            return $model;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    protected function with($query): Builder
    {
        return $query->with([
            'currencies',
            'banks',
            'user',
        ]);
    }
}
