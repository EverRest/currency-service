<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Services\Eloquent\BankService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BankController extends Controller
{
    /**
     * @param BankService $bankService
     */
    public function __construct(private readonly BankService $bankService)
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $response = $this->bankService->list($request->all());

        return Response::data($response);
    }

    /**
     * @param Bank $bank
     *
     * @return JsonResponse
     */
    public function item(Bank $bank): JsonResponse
    {
        return Response::data($bank);
    }
}
