<?php

namespace Tests\Feature\Support\Permission;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminMenu;
use App\Support\Permission\PermissionBuilder;
use App\Support\Routing\RouteParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionException;
use Tests\TestCase;

class PermissionBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected string $routeAction = AuthController::class . '@login';

    public function testFromRoute()
    {
        self::assertEquals('route:Admin/Auth@login', PermissionBuilder::fromRoute($this->routeAction));
    }

    public function testFromRouteParser()
    {
        $parser = new RouteParser($this->routeAction);
        self::assertEquals('route:Admin/Auth@login', PermissionBuilder::fromRouteParser($parser));
    }

    public function testFromMenu()
    {
        $menu = AdminMenu::query()->first();

        self::assertEquals('menu:admin_menus@'. $menu['id'], PermissionBuilder::fromMenu($menu));
    }

    public function testIsRoute()
    {
        self::assertTrue(PermissionBuilder::isRoute('route:Admin/Auth@login'));
    }

    public function testIsMenu()
    {
        self::assertTrue(PermissionBuilder::isMenu('menu:admin_menus@1'));
    }

    public function testMenuId()
    {
        self::assertEquals(1, PermissionBuilder::menuId('menu:admin_menus@1'));
    }

    /**
     * @throws ReflectionException
     */
    public function testActionData()
    {
        $actionData = PermissionBuilder::actionData($this->routeAction);
        self::assertArrayHasKey('name', $actionData);
        self::assertArrayHasKey('controller', $actionData);
        self::assertArrayHasKey('action', $actionData);
        self::assertArrayHasKey('controller_title', $actionData);
        self::assertArrayHasKey('action_title', $actionData);

        self::assertEquals('route:Admin/Auth@login', $actionData['name']);
        self::assertEquals('Admin/Auth', $actionData['controller']);
        self::assertEquals('login', $actionData['action']);
    }
}
