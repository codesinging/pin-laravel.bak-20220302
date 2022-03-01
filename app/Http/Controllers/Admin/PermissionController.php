<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Support\Permission\PermissionUpdater;
use Illuminate\Http\JsonResponse;
use ReflectionException;

class PermissionController extends Controller
{
    /**
     * @title 更新权限规则
     * @throws ReflectionException
     */
    public function update(string $type = 'all'): JsonResponse
    {
        switch ($type) {
            case 'route':
                PermissionUpdater::updateAdminRoutePermissions();
                break;
            case 'menu':
                PermissionUpdater::updateAdminMenuPermissions();
                break;
            default:
                PermissionUpdater::updateAdminPermissions();
                break;
        }

        return $this->success('更新成功');
    }
}
