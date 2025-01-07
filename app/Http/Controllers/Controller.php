<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function apiResponse($data = null, $message = null, $status = true, $statusCode = 200)
    {
        $response = [
            'success' => $status,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }
}
