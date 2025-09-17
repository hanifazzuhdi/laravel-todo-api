<?php

namespace App\Http\Controllers\Api\V1;

use App\Exports\TodoExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TodoRequest;
use App\Http\Resources\V1\TodoCollection;
use App\Http\Resources\V1\TodoResource;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    private TodoService $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $todos = $this->todoService->getAllTodos($request->all());
            $todosCollection = TodoCollection::make($todos);

            return $this->responseApi($todosCollection, 'Todos retrieved successfully');
        } catch (\Throwable $th) {
            return $this->responseApi(null, 'Failed to retrieve todos: ' . $th->getMessage(), 500);
        }
    }

    public function store(TodoRequest $request): JsonResponse
    {
        try {
            $newTodo = $this->todoService->createTodo($request->all());
            $todoResource = TodoResource::make($newTodo);

            return $this->responseApi($todoResource, 'Todo created successfully', 201);
        } catch (\Throwable $th) {
            return $this->responseApi(null, 'Failed to create todo: ' . $th->getMessage(), 500);
        }
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse | JsonResponse
    {
        try {
            $todos = $this->todoService->getAllTodos($request->all());
            $todosCollection = TodoCollection::make($todos);

            return (new TodoExport($todosCollection->toArray($request)))->download('todos_' . date('Y_m_d_H_i_s') . '.xlsx');
        } catch (\Throwable $th) {
            return $this->responseApi(null, 'Failed to export todos: ' . $th->getMessage(), 500);
        }
    }
}
