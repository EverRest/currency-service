<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Register;
use App\Services\Eloquent\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param Register $request
     *
     * @return JsonResponse
     */
    public function __invoke(Register $request): JsonResponse
    {
        $attributes = $request->validated();
        $user = $this->userService->firstOrCreate([
            'name' => Arr::get($attributes, 'name'),
            'email' => Arr::get($attributes, 'email'),
            'password' => Hash::make(Arr::get($attributes, 'password')),
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }
}
