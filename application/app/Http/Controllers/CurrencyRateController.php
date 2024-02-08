<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Eloquent\CurrencyRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CurrencyRateController extends Controller
{
    /**
     * @param CurrencyRateService $currencyRateService
     */
    public function __construct(
        private readonly CurrencyRateService $currencyRateService
    )
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $response = $this->currencyRateService->list($request->all());

        return Response::data($response);
    }
}
