<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Exceptions\ErrorCode;
use App\Models\Admin;
use App\Models\AdminPermission;
use App\Support\Permission\PermissionName;
use App\Support\Routing\RouteParser;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return Response|RedirectResponse|JsonResponse
     * @throws ApiException
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        /** @var Admin $admin */
        $admin = $request->user();

        $permission = PermissionName::fromRoute($request->route());

        if (!is_null(AdminPermission::query()->where('name', $permission)->first()) && !$admin->can($permission)) {
            throw new ApiException('无访问权限', ErrorCode::PERMISSION_NO_AUTHORIZATION);
        }

        return $next($request);
    }
}
