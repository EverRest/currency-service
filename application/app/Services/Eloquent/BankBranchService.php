<?php
declare(strict_types=1);

namespace App\Services\Eloquent;

use App\Models\BankBranch;
use App\Services\Abstracts\ServiceWithEloquentModel;
use App\Services\Http\GeoService;
use Illuminate\Support\Arr;
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
     * @param float $radius
     *
     * @return Collection
     */
    public function getClosestBanks(float $radius = 5): Collection
    {
        $geoData = GeoService::getGeoData();
        $lat = Arr::get($geoData, 'lat');
        $lng = Arr::get($geoData, 'lng');

        return  BankBranch::select('bank_branches.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) as distance',
                [$lat, $lng, $lat]
            )
            ->havingRaw('6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat))) <= ?', [$lat, $lng, $lat, $radius])
            ->groupBy('bank_branches.id')
            ->orderBy('distance')
            ->get();
    }
}
