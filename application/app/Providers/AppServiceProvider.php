<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('data', function ($data, $status = 200, $headers = [], $options = 0) {
            return response()->json(['data' => $data], $status, $headers, $options);
        });
    }
}
