<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NytBestSellersRequest;
use App\Services\NytBestSellersService;
use Illuminate\Http\JsonResponse;
use Throwable;

class BestSellersController extends Controller
{
    /**
     * @param NytBestSellersService $service
     */
    public function __construct(protected NytBestSellersService $service) {}

    /**
     * @param NytBestSellersRequest $request
     * @return JsonResponse
     */
    public function index(NytBestSellersRequest $request): JsonResponse
    {
        try {
            $data = $this->service->fetch($request->validated());
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обращении к NYT API. '. $e->getMessage(),
            ], $e->getCode());
        }
    }
}
