<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Permission;

use App\Support\Reflection\ClassReflection;
use App\Support\Routing\RouteParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use ReflectionException;

class PermissionBuilder
{
    const PREFIX_ROUTE = 'route';
    const PREFIX_MENU = 'menu';

    /**
     * 从路由构建
     *
     * @param Route|string|null $route
     *
     * @return string
     */
    public static function fromRoute(Route|string $route = null): string
    {
        return self::fromRouteParser(new RouteParser($route));
    }

    /**
     * 从路由解析器构建
     *
     * @param RouteParser $parser
     *
     * @return string
     */
    #[Pure]
    public static function fromRouteParser(RouteParser $parser): string
    {
        return sprintf('%s:%s@%s', self::PREFIX_ROUTE, $parser->controller(), $parser->action());
    }

    /**
     * 从菜单构建
     *
     * @param Model $model
     *
     * @return string
     */
    public static function fromMenu(Model $model): string
    {
        return sprintf('%s:%s@%s', self::PREFIX_MENU, $model->getTable(), $model['id']);
    }

    /**
     * 是否路由
     *
     * @param string $permissionName
     *
     * @return bool
     */
    #[Pure]
    public static function isRoute(string $permissionName): bool
    {
        return Str::startsWith($permissionName, self::PREFIX_ROUTE);
    }

    /**
     * 是否菜单
     *
     * @param string $permissionName
     *
     * @return bool
     */
    #[Pure]
    public static function isMenu(string $permissionName): bool
    {
        return Str::startsWith($permissionName, self::PREFIX_MENU);
    }

    /**
     * 从菜单权限名称中获取菜单 ID
     *
     * @param string $permissionName
     *
     * @return string
     */
    #[Pure]
    public static function menuId(string $permissionName): string
    {
        return Str::afterLast($permissionName, '@');
    }

    /**
     * @throws ReflectionException
     */
    public static function actionData(Route|string $route): ?array
    {
        $parser = new RouteParser($route);

        $reflection = new ClassReflection($parser->class());

        if (!is_null($controllerTitle = $reflection->classTitle()) && !is_null($actionTitle = $reflection->methodTitle($parser->action()))) {
            return [
                'name' => self::fromRouteParser($parser),
                'controller' => $parser->controller(),
                'action' => $parser->action(),
                'controller_title' => $controllerTitle,
                'action_title' => $actionTitle,
            ];
        }

        return null;
    }
}
