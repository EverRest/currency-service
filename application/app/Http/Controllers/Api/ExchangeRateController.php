<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Eloquent\BankService;
use App\Services\Eloquent\ExchangeRateService;
use App\Services\Eloquent\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class ExchangeRateController extends Controller
{
    /**
     * @param ExchangeRateService $ExchangeRateService
     * @param CurrencyService $currencyService
     * @param BankService $bankService
     */
    public function __construct(
        private readonly ExchangeRateService $ExchangeRateService,
        private readonly CurrencyService     $currencyService,
        private readonly BankService         $bankService,
    )
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function currentRates(Request $request): JsonResponse
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function statistic(Request $request): JsonResponse
    {
        $fromDate = $request->has('from') ? Carbon::make($request->get('from')) : Carbon::now()->subMonth();
        $toDate = $request->has('to') ? Carbon::make($request->get('to')) : Carbon::now();
        $bankIds = $request->has('bank_id') ? $request->get('bank_id', []) :
            $this->bankService->list()->pluck('id')->toArray();
        $currencyIds = $request->has('currency_id') ? $request->get('currency_id', []) :
            $this->currencyService->list()->pluck('id')->toArray();
        $response = $this->ExchangeRateService
            ->getStatisticByPeriod($fromDate, $toDate, $bankIds, $currencyIds);

        return Response::data($response);
    }
}