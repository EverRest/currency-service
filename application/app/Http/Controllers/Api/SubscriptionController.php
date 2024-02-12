<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\Store;
use App\Http\Requests\Subscription\Update;
use App\Models\Subscription;
use App\Services\Eloquent\SubscriptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Throwable;

class SubscriptionController extends Controller
{
    /**
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {

        $response = $this->subscriptionService
            ->list($request->all());

        return Response::data($response);
    }

    /**
     * @param Store $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $attributes = [
            ...$request->validated(),
            'user_id' => $request->user()->id
        ];
        $response = $this->subscriptionService
            ->store($attributes);

        return Response::data($response);
    }

    /**
     * @param Subscription $subscription
     * @param Update $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Subscription $subscription, Update $request): JsonResponse
    {
        $attributes = $request->validated();
        $response = $this->subscriptionService
            ->update($subscription, $attributes);

        return Response::data($response);
    }

    /**
     * @param Subscription $subscription
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        $response = $this->subscriptionService
            ->destroy($subscription);

        return Response::data($response);
    }
}
