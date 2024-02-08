<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Eloquent\BankService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return response()->json(['data' => $response]);
    }
}
