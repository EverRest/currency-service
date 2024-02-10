<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankBranch\ClosestBank;
use App\Services\Eloquent\BankBranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
        $attributes = $request->all();
        $response = $this->bankBranchService
            ->list($attributes);

        return Response::data($response);
    }

    /**
     * @param ClosestBank $request
     *
     * @return JsonResponse
     */
    public function getClosestBanks(ClosestBank $request): JsonResponse
    {
        $response = $this->bankBranchService
            ->getClosestBanks($request->lat, $request->lng, $request->radius);

        return Response::data($response);
    }
}
