<?php

namespace App\Services;

class Response
{
    public static function success($data = [])
    {
        return response()->json($data, 200);
    }

    public static function message($message = [], $status = 200)
    {
        return response()->json([
            'message' => $message
        ], $status);
    }

    public static function inputFailed($message = 'invalid field')
    {
        return response()->json([
            'message' => $message
        ], 422);
    }
}
