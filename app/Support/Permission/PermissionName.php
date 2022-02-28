<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Permission;

use App\Support\Routing\RouteParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use JetBrains\PhpStorm\Pure;

class PermissionName
{
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
        return sprintf('%s:%s/%s@%s', 'route', $parser->module(), $parser->controller(), $parser->action());
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
        return sprintf('%s:%s', 'menu', $model['id']);
    }
}
