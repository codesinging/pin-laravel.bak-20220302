<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Support\Routing;

use App\Http\Controllers\Admin\AuthController;
use App\Support\Routing\RouteParser;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use ReflectionException;
use Tests\TestCase;

class RouteParserTest extends TestCase
{
    protected string $routeAction = AuthController::class . '@login';

    protected string $routeClass = AuthController::class;

    public function testRoutes()
    {
        $routes = RouteParser::routes('api/admin');

        /** @var Route $route */
        $route = Arr::random($routes);

        self::assertEquals('api/admin', $route->getPrefix());
    }

    public function testRoute()
    {
        self::assertInstanceOf(Route::class, RouteParser::route($this->routeAction));
    }

    /**
     * @throws ReflectionException
     */
    public function testRouteParser()
    {
        $parser = new RouteParser($this->routeAction);

        self::assertEquals('Admin', $parser->module());
        self::assertEquals('Auth', $parser->controller());
        self::assertEquals('login', $parser->action());
        self::assertEquals('管理员认证', $parser->controllerTitle());
        self::assertEquals('管理员登录', $parser->actionTitle());
    }
}
