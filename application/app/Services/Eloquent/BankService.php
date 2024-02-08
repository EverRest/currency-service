<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Bank;
use App\Services\Abstracts\ServiceWithEloquentModel;

class BankService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = Bank::class;

    /**
     * @param int $externalId
     *
     * @return bool
     */
    public function existsByExternalId(int $externalId): bool
    {
        return $this->query()->where('external_id', $externalId)->exists();
    }
}
