<?php
declare(strict_types=1);

namespace App\Services\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Pagination\Paginator;
use Throwable;

class ServiceWithEloquentModel
{
    /**
     * @var string
     */
    protected string $model;

    /**
     * Get a list of models
     *
     * @param array $data
     *
     * @return Collection
     */
    public function list(array $data = []): Collection
    {
        $query = $this->search($data);
        $this->with($query);
        $this->filter(
            $query,
            Arr::except(
                $data,
                [
                    Config::get('pagination.search_key'),
                    Config::get('pagination.sort_key'),
                    Config::get('pagination.order_key'),
                    Config::get('pagination.limit_key'),
                    Config::get('pagination.page_key')
                ]
            )
        );
        $this->sort(
            $query,
            Arr::only($data, [Config::get("pagination.sort_key"), Config::get("pagination.order_key")]),
        );
        return $query->get();
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        /**
         * @var Model $model
         */
        $model = App::make($this->model);

        return $model::query();
    }

    /**
     * @param array $data
     *
     * @return Builder
     */
    protected function search(array $data): Builder
    {
        return $this->query();
    }

    /**
     * @param $query
     * @param array $filter
     *
     * @return Builder
     */
    protected function filter($query, array $filter): Builder
    {
        $query->when($filter, fn($query) => $this->applyFilter($query, $filter));

        return $query;
    }

    /**
     * @param mixed $query
     * @param array $filter
     *
     * @return mixed
     */
    protected function applyFilter(mixed $query, array $filter): mixed
    {
        foreach ($filter as $filterKey => $filterValue) {
            if (!is_string($filterKey)) {
                continue;
            }

            if (is_array($filterValue)) {
                $query->whereIn($filterKey, $filterValue);
            } else {
                $query->where($filterKey, $filterValue);
            }
        }

        return $query;
    }

    /**
     * @param $query
     * @param array $data
     *
     * @return Paginator
     */
    protected function paginate($query, array $data): Paginator
    {
        $limit = Arr::get($data, Config::get('pagination.limit_key')) ?: Config::get('pagination.limit_per_page');
        return $query->paginate($limit);
    }

    /**
     * @param $query
     * @param array $data
     *
     * @return Builder
     */
    protected function sort($query, array $data): Builder
    {
        $sort = $this->getSortColumn($data);
        $order = $this->getDirectionColumn($data);
        $query->when($sort, function ($query) use ($sort, $order) {
            return $query->orderBy($sort, $order);
        });

        return $query;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function getSortColumn(array $data): string
    {
        return Arr::get($data, Config::get('pagination.sort_key'), Config::get('pagination.default_field'));
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function getDirectionColumn(array $data): string
    {
        return Arr::get($data, Config::get('pagination.order_key'), Config::get('pagination.order_direction'));
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    protected function with($query): Builder
    {
        return $query;
    }

    /**
     * @return Model
     */
    protected function model(): Model
    {
        return new $this->model();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function store(array $data): Model
    {
        return $this->model::create($data)->refresh();
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function firstOrCreate(array $data): Model
    {
        return $this->model::firstOrCreate($data);
    }

    /**
     * Update and refresh model
     *
     * @param Model $model
     * @param array $data
     *
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        $model->fill($data)->save();
        return $model->refresh();
    }

    /**
     * Patch and refresh model
     *
     * @param Model $model
     * @param string $fieldName
     * @param array $data
     *
     * @return Model
     */
    public function patch(Model $model, string $fieldName, mixed $data): Model
    {
        return $this->update($model, [$fieldName => $data]);
    }

    /**
     * @param Model $model
     * @param array $data
     *
     * @return Model
     * @throws Throwable
     */
    public function updateOrFail(Model $model, array $data): Model
    {
        $model->updateOrFail($data);
        return $model;
    }

    /**
     * @param int $id
     *
     * @return Model
     */
    public function findOrFail(int $id): Model
    {
        return $this->model::findOrFail($id);
    }

    /**
     * @return void
     */
    public function destroyAll(): void
    {
        in_array(SoftDeletes::class, class_uses($this->query()->getModel()), true) ?
            $this->model::all()->each(fn(Model $model) => $model->delete()) :
            $this->model::truncate();
    }
    /**
     * Delete the model from the database within a transaction.
     *
     * @param Model $model
     * @param bool $force
     *
     * @return Model
     * @throws Throwable
     */
    public function destroy(Model $model, bool $force = false): Model
    {
        if ($force) {
            $model->forceDelete();
        } else {
            $model->deleteOrFail();
        }
        return $model;
    }
}
