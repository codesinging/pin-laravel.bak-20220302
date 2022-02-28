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

    public function testRouteParser()
    {
        $parser = new RouteParser($this->routeAction);

        self::assertEquals(AuthController::class, $parser->class());
        self::assertEquals('Admin', $parser->module());
        self::assertEquals('Auth', $parser->controller());
        self::assertEquals('login', $parser->action());
    }

    public function testRule()
    {
        $parser = new RouteParser($this->routeAction);
        self::assertEquals('route:Admin/Auth@login', $parser->rule());
    }
}
