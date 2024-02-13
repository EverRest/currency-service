<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangeRate\Current;
use App\Http\Requests\ExchangeRate\Statistic;
use App\Services\Eloquent\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class ExchangeRateController extends Controller
{
    /**
     * @param ExchangeRateService $ExchangeRateService
     */
    public function __construct(
        private readonly ExchangeRateService $ExchangeRateService,
    )
    {
    }

    /**
     * @param Current $request
     *
     * @return JsonResponse
     */
    public function currentRates(Current $request): JsonResponse
    {
        $response = $this->ExchangeRateService
            ->list($request->all());

        return Response::data($response);
    }

    /**
     * @return JsonResponse
     */
    public function avgRates(): JsonResponse
    {
        $response = $this->ExchangeRateService
            ->getAvgRates();

        return Response::data($response);
    }

    /**
     * @param Statistic $request
     *
     * @return JsonResponse
     */
    public function statistic(Statistic $request): JsonResponse
    {
        $response = $this->ExchangeRateService
            ->getStatisticByPeriod(
                $request->from,
                $request->to,
                $request->bank_id,
                $request->currency_id
            );

        return Response::data($response);
    }
}
