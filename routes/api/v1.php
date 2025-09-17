<?php

use App\Http\Controllers\Api\V1\TodoChartController;
use App\Http\Controllers\Api\V1\TodoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /**
     * API Resource Routes for Todo Management
     */
    Route::apiResource('todos', TodoController::class)->only(['index', 'store']);

    /**
     * API for provide chart data
     */
    Route::get('todos/chart', [TodoChartController::class, 'index']);

    /**
     * API for exporting todos
     */
    Route::get('todos/export', [TodoController::class, 'export'])->name('api.todos.export');
});
