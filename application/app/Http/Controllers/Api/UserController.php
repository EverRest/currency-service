<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Update;
use App\Models\User;
use App\Services\Eloquent\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * @param UserService $userService
     */
    public function __construct(
        private readonly UserService $userService,
    )
    {}

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getCurrentUser(Request $request)
    {
        $userId = $request->user()->id;
        $user = $this->userService->findOrFail($userId);

        return Response::data($user);
    }

    /**
     * @param User $user
     * @param Update $request
     *
     * @return JsonResponse
     */
    public function update(User $user, Update $request): JsonResponse
    {
        $user = $this->userService
            ->update($user, $request->validated());

        return Response::data($user);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function toggleCriticalRateChangeAlert(User $user): JsonResponse
    {
        $user = $this->userService
            ->toggleCriticalRateChangeSubscription($user);

        return Response::data($user);
    }
}
