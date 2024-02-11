<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\Bank;
use App\Services\Super\ServiceWithEloquentModel;
use App\Traits\HasFindByCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BankService extends ServiceWithEloquentModel
{
    use HasFindByCode;
    private const NBU_BANK_CODE = 'nbu';

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

    /**
     * @return Model|Bank
     */
    public function getNbuBank(): Model|Bank
    {
        return $this->findByCode(self::NBU_BANK_CODE);
    }

    /**
     * @return Collection
     */
    public function getNotNbuBanks(): Collection
    {
        return $this->query()->where('code', '!=', self::NBU_BANK_CODE)->get();
    }
}
