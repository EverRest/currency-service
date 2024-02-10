<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LogoutController extends Controller
{
    private const MESSAGE = 'Successfully logged out';
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return Response::data([
            'message' => self::MESSAGE
        ]);
    }
}
