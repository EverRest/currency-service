<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\Update;
use App\Models\User;
use App\Services\Eloquent\UserService;
use Illuminate\Http\JsonResponse;
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
     * @param User $user
     * @param Update $request
     *
     * @return JsonResponse
     */
    public function update(User $user, Update $request): JsonResponse
    {
        $user = $this->userService
            ->update($user, $request->validated());

        return Response::data([
            'data' => $user
        ]);
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

        return Response::data([
            'data' => $user
        ]);
    }
}
