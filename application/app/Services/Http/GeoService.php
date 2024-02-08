<?php
declare(strict_types=1);

namespace App\Services\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GeoService
{
    /**
     * @return array
     */
    public static function getGeoData(): array
    {
        $ip = request()->ip();
        $geoData = [
            'lat' => null,
            'lng' => null,
        ];
        $response = Http::get("http://ip-api.com/json/$ip?fields=lat,lon");
        if ($response->successful()) {
            $data = $response->json();
            $latitude = Arr::get($data, 'lat');
            $longitude = Arr::get($data, 'lon');
            Arr::set($geoData, 'lat', $latitude);
            Arr::set($geoData, 'lng', $longitude);
        }

        return $geoData;
    }
}
