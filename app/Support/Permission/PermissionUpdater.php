<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Permission;

use App\Models\AdminAction;
use App\Models\AdminMenu;
use App\Models\AdminPermission;
use App\Support\Routing\RouteParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ReflectionException;

class PermissionUpdater
{
    /**
     * 更新管理后台所有权限
     *
     * @return void
     * @throws ReflectionException
     */
    public static function updateAdminPermissions()
    {
        self::updateAdminRoutePermissions();
        self::updateAdminMenuPermissions();
    }

    /**
     * 更新管理后台动作权限
     *
     * @throws ReflectionException
     */
    public static function updateAdminRoutePermissions()
    {
        self::storeRoutePermissions('api/admin');
    }

    /**
     * 更新管理后台菜单权限
     *
     * @return void
     */
    public static function updateAdminMenuPermissions()
    {
        self::storeMenuPermissions(AdminMenu::query());
    }

    /**
     * @throws ReflectionException
     */
    protected static function storeRoutePermissions(string $prefix)
    {
        $routes = RouteParser::routes($prefix);

        foreach ($routes as $route) {
            if ($action = PermissionBuilder::actionData($route)) {
                AdminAction::new()->store($action);
            }
        }
    }

    protected static function storeMenuPermissions(Builder $menu)
    {
        $menus = $menu->where('status', true)->get();

        $menus->each(fn(Model $menu) => AdminPermission::create([
            'name' => PermissionBuilder::fromMenu($menu)
        ]));
    }
}
