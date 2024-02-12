<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use App\Services\Helper\CriticalRateChangeChecker;

class AppServiceProvider extends ServiceProvider
{
    private const DATA_KEY = 'data';

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app
            ->singleton(
                CriticalRateChangeChecker::class,
                fn() => new CriticalRateChangeChecker()
            );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro(
            self::DATA_KEY,
            function ($data, $status = 200, $headers = [], $options = 0) {
                return response()
                    ->json(
                        [self::DATA_KEY => $data],
                        $status,
                        $headers,
                        $options
                    );
            }
        );
    }
}
