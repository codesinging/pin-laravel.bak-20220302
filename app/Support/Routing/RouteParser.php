<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Routing;

use Illuminate\Routing\Route as RouteClass;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

class RouteParser
{
    protected RouteClass $route;

    protected ?string $class;
    protected ?string $module;
    protected ?string $controller;
    protected ?string $action;

    /**
     * @param RouteClass|string|null $route
     */
    public function __construct(RouteClass|string $route = null)
    {
        $this->route = empty($route) ? Request::route() : (is_string($route) ? self::route($route) : $route);
        $this->parse();
    }

    /**
     * @param string|null $prefix
     *
     * @return RouteClass[]
     */
    public static function routes(string $prefix = null): array
    {
        /** @var RouteClass[] $routes */
        $routes = Route::getRoutes();

        return $prefix
            ? collect($routes)->filter(fn(RouteClass $route) => $route->getPrefix() === $prefix)->toArray()
            : $routes;
    }

    /**
     * @param string $action
     *
     * @return RouteClass|null
     */
    public static function route(string $action): ?RouteClass
    {
        return Route::getRoutes()->getByAction($action);
    }

    /**
     * @return void
     */
    private function parse()
    {
        list($class, $action) = explode('@', $this->route->getActionName());

        $this->class = $class;
        $this->action = $action;

        $name = Str::after($class, 'App\\Http\\Controllers\\');
        $name = Str::beforeLast($name, 'Controller');

        if (str_contains($name, '\\')) {
            $this->controller = Str::afterLast($name, '\\');
            $this->module = Str::beforeLast($name, '\\');
        } else {
            $this->controller = $name;
            $this->module = '';
        }
    }

    /**
     * @return string|null
     */
    public function class(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function module(): ?string
    {
        return $this->module;
    }

    /**
     * @return string|null
     */
    public function controller(): ?string
    {
        return $this->controller;
    }

    /**
     * @return string|null
     */
    public function action(): ?string
    {
        return $this->action;
    }
}
