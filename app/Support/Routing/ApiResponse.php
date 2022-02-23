<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Routing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * 返回成功的 JSON 响应数据
     *
     * @param string|Model|array|null $message
     * @param array|Model|string|null $data
     *
     * @return JsonResponse
     */
    public static function success(string|Model|array $message = null, array|Model|string $data = null): JsonResponse
    {
        $code = 0;
        is_string($message) or list($data, $message) = [$message, $data];
        return response()->json(compact('code', 'message', 'data'));
    }

    /**
     * 返回错误的 JSON 响应数据
     *
     * @param string|null $message
     * @param int $code
     * @param $data
     *
     * @return JsonResponse
     */
    public static function error(string $message = null, int $code = -1, $data = null): JsonResponse
    {
        return response()->json(compact('message', 'code', 'data'));
    }
}
