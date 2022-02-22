<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Routing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;

class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 返回成功的 JSON 响应数据
     *
     * @param string|Model|array|null $message
     * @param array|Model|string|null $data
     *
     * @return JsonResponse
     */
    protected function success(string|Model|array $message = null, array|Model|string $data = null): JsonResponse
    {
        return Response::success($message, $data);
    }

    /**
     * 返回错误的 JSON 响应数据
     *
     * @param string|null $message
     * @param int $code
     * @param mixed|null $data
     *
     * @return JsonResponse
     */
    protected function error(string $message = null, int $code = -1, mixed $data = null): JsonResponse
    {
        return Response::error($message, $code, $data);
    }
}
