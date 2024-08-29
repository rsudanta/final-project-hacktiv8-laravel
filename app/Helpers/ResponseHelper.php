<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function jsonResponse($status, $message, $data = null, $statusCode = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }
}
