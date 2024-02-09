<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasFindByCode
{
    /**
     * @return Builder
     */
    abstract public function query(): Builder;

    /**
     * @param string $slug
     *
     * @return Model
     */
    public function findByCode(string $slug): Model
    {
        return $this->query()->where('code', $slug)->firstOrFail();
    }
}
