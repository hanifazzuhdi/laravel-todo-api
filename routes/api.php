<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app_name'  => config('app.name', 'Todo API'),
        'version'   => config('app.version', '1.0.0'),
        'timestamp' => now()
    ]);
});

require __DIR__ . '/api/v1.php';
