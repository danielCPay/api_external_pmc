<?php

namespace App\Http\Responses;

class ApiResponse
{
    public $data;
    public $message;
    public $success;
    public function __construct($data = null, $message = '', $success = true)
    {
        $this->message = $message;
        $this->data = $data;
        $this->success = $success;
    }
    public static function success($message = 'Success', $statusCode = 200, $data = [], $error = false)
    {
        return response()->json(
            [
                'message'    => $message,
                'statusCode' => $statusCode,
                'error'      => $error,
                'data'       => $data
            ],
            $statusCode
        );
    }

    public static function error($message = 'Error', $statusCode, $data = [])
    {
        return response()->json(
            [
                'message'    => $message,
                'statusCode' => $statusCode,
                'error'     => true,
                'data'      => $data
            ],
            $statusCode
        );
    }

    public static function object($data = [])
    {
        return response()->json(
            [
                'data'      => $data
            ]
        );
    }

    public static function emptydata($message = 'Error', $status)
    {
        return response()->json(
            [
                'message'    => $message,
                'status' => 0,
                'error'     => [
                    "message" => $message,
                    "code" => $status
                ]
            ],
            $status
        );
    }
}
