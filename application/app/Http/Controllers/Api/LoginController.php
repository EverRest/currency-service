<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @param Login $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(Login $request): JsonResponse
    {
        $attributes = $request->validated();
        if (!Auth::attempt(Arr::only($attributes, ['email', 'password']))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $user = Auth::user();

        return Response::data([
            'token' => $user->createToken('api-token')
                ->plainTextToken,
            'user' => $user,
        ]);
    }
}

