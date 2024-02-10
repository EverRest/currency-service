<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\BankBranch;
use App\Services\Super\ServiceWithEloquentModel;
use Illuminate\Support\Collection;

class BankBranchService extends ServiceWithEloquentModel
{
    /**
     * @var string $model
     */
    protected string $model = BankBranch::class;

    /**
     * Get the closest banks.
     *
     * @param float $lat
     * @param float $lng
     * @param float $radius
     *
     * @return Collection
     */
    public function getClosestBanks(float $lat, float $lng, float $radius = 1): Collection
    {
        return BankBranch::select('bank_branches.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) as distance',
                [$lat, $lng, $lat]
            )
            ->whereRaw(
                '6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat))) <= ?',
                [$lat, $lng, $lat, $radius]
            )
            ->groupBy('bank_branches.id')
            ->orderBy('distance')
            ->get();
    }
}
