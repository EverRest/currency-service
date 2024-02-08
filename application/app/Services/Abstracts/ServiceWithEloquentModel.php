<?php
declare(strict_types=1);

namespace App\Services\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Throwable;

class ServiceWithEloquentModel
{
    protected string $model;

    /**
     * Get a list of models.
     *
     * @param array $data
     *
     * @return Collection
     */
    public function list(array $data = []): Collection
    {
        $query = $this->search($data);
        $this->with($query);
        $this->filter($query, Arr::except($data, $this->getPaginationKeys()));
        $this->sort($query, Arr::only($data, [$this->getSortKey(), $this->getOrderKey()]));
        return $query->get();
    }

    /**
     * Get the pagination keys from Config.
     *
     * @return array
     */
    protected function getPaginationKeys(): array
    {
        return [
            Config::get('pagination'),
        ];
    }

    /**
     * Get the sort key from Config.
     *
     * @return string
     */
    protected function getSortKey(): string
    {
        return Config::get('pagination.sort_key');
    }

    /**
     * Get the order key from Config.
     *
     * @return string
     */
    protected function getOrderKey(): string
    {
        return Config::get('pagination.order_key');
    }

    /**
     * Get a new query builder instance for the model.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return App::make($this->model)::query();
    }

    /**
     * Apply search conditions to the query.
     *
     * @param array $data
     *
     * @return Builder
     */
    protected function search(array $data): Builder
    {
        return $this->query();
    }

    /**
     * Apply filters to the query.
     *
     * @param mixed $query
     * @param array $filter
     *
     * @return Builder
     */
    protected function filter(mixed $query, array $filter): Builder
    {
        return $query->when($filter, fn ($query) => $this->applyFilter($query, $filter));
    }

    /**
     * Apply a filter to the query.
     *
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

            $query->when(is_array($filterValue), fn ($query) => $query->whereIn($filterKey, $filterValue))
                ->when(!is_array($filterValue), fn ($query) => $query->where($filterKey, $filterValue));
        }

        return $query;
    }

    /**
     * Paginate the given query.
     *
     * @param mixed $query
     * @param array $data
     *
     * @return Paginator
     */
    protected function paginate(mixed $query, array $data): Paginator
    {
        $limit = Arr::get($data, Config::get('pagination.limit_key'), Config::get('pagination.limit_per_page'));
        return $query->paginate($limit);
    }

    /**
     * Apply sorting to the query.
     *
     * @param mixed $query
     * @param array $data
     *
     * @return Builder
     */
    protected function sort(mixed $query, array $data): Builder
    {
        $sort = $this->getSortColumn($data);
        $order = $this->getDirectionColumn($data);
        return $query->when($sort, fn ($query) => $query->orderBy($sort, $order));
    }

    /**
     * Get the sort column from the data.
     *
     * @param array $data
     *
     * @return string
     */
    protected function getSortColumn(array $data): string
    {
        return Arr::get($data, Config::get('pagination.sort_key'), Config::get('pagination.default_field'));
    }

    /**
     * Get the direction column from the data.
     *
     * @param array $data
     *
     * @return string
     */
    protected function getDirectionColumn(array $data): string
    {
        return Arr::get($data, Config::get('pagination.order_key'), Config::get('pagination.order_direction'));
    }

    /**
     * Apply eager loading to the query.
     *
     * @param mixed $query
     *
     * @return Builder
     */
    protected function with(mixed $query): Builder
    {
        return $query;
    }

    /**
     * Get a new model instance.
     *
     * @return Model
     */
    protected function model(): Model
    {
        return new $this->model();
    }

    /**
     * Get the count of records in the query.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Store a new record in the database.
     *
     * @param array $data
     *
     * @return Model
     */
    public function store(array $data): Model
    {
        return $this->model::create($data)->refresh();
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param array $data
     *
     * @return Model
     */
    public function firstOrCreate(array $data): Model
    {
        return $this->model::firstOrCreate($data);
    }

    /**
     * Update and refresh the given model.
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
     * Patch and refresh the given model.
     *
     * @param Model $model
     * @param string $fieldName
     * @param mixed $data
     *
     * @return Model
     */
    public function patch(Model $model, string $fieldName, mixed $data): Model
    {
        return $this->update($model, [$fieldName => $data]);
    }

    /**
     * Update or throw an exception if it fails.
     *
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
     * Find a model by its primary key or throw an exception.
     *
     * @param int $id
     *
     * @return Model
     */
    public function findOrFail(int $id): Model
    {
        return $this->model::findOrFail($id);
    }

    /**
     * Destroy all records in the query.
     *
     * @return void
     */
    public function destroyAll(): void
    {
        $modelQuery = $this->query();

        $modelQuery->when(
            in_array(SoftDeletes::class, class_uses($modelQuery->getModel()), true),
            fn ($query) => $query->each(fn (Model $model) => $model->delete())
        )->unless(
            in_array(SoftDeletes::class, class_uses($modelQuery->getModel()), true),
            fn ($query) => $query->truncate()
        );
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
        $force ? $model->forceDelete() : $model->deleteOrFail();
        return $model;
    }
}
