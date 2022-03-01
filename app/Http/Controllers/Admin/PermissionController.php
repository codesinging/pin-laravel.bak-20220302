<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminAction;
use App\Support\Permission\PermissionUpdater;
use Illuminate\Http\JsonResponse;
use ReflectionException;

/**
 * @title 权限管理
 */
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

    /**
     * @title 获取操作权限列表
     * @return JsonResponse
     */
    public function actions(): JsonResponse
    {
        $actions = AdminAction::all()->toArray();

        return $this->success('获取操作权限列表成功', $actions);
    }
}
