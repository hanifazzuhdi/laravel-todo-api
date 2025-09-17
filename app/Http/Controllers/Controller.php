<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function responseApi($data, $message = null, $status = 200): \Illuminate\Http\JsonResponse
    {
        $response = [
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data'    => $data,
            'status'  => $status,
        ];

        return response()->json($response, $status);
    }
}
