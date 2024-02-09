<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Eloquent\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CurrencyController extends Controller
{
    /**
     * @param CurrencyService $currencyService
     */
    public function __construct(private readonly CurrencyService $currencyService)
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $response = $this->currencyService
            ->list($request->all());

        return Response::data($response);
    }
}
