<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Eloquent\BankBranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankBranchController extends Controller
{
    /**
     * @param BankBranchService $bankBranchService
     */
    public function __construct(private readonly BankBranchService $bankBranchService)
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $response = $this->bankBranchService->list($request->all());

        return response()->json(['data' => $response]);
    }
}
