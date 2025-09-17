<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoChartController extends Controller
{
    private TodoService $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $type = $request->query('type', 'status');
            $chartData = $this->todoService->prepareChartData($type);

            return $this->responseApi($chartData, 'Chart data retrieved successfully');
        } catch (\Throwable $th) {
            return $this->responseApi(null, 'Failed to retrieve chart data: ' . $th->getMessage(), $th->getCode() ?: 500);
        }
    }
}
