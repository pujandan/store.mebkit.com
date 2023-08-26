<?php

namespace App\Helpers;

use Exception;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'code' => 200,
        'status' => 'success',
        'message' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($data = null, $message = null)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * Give error response.
     */
    public static function error($message = null, $code = 400, $data = null)
    {
        self::$response['code'] = $code;
        self::$response['status'] = 'error';
        self::$response['message'] = $message;
        if ($data) {
            self::$response['data'] = $data;
        }
        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * Give exception response.
     */
    public static function exception(\Exception $e, $code = 404, $data = null)
    {
        self::$response['code'] = $e->getCode() != 0 ? $e->getCode() : $code ?? 404;
        self::$response['status'] = 'error';
        self::$response['message'] = $e->getMessage();
        if ($data) {
            self::$response['data'] = $data;
        }
        return response()->json(self::$response, self::$response['code']);
    }
}
