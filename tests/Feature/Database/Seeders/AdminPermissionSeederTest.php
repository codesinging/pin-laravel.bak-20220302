<?php

namespace Tests\Feature\Database\Seeders;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminMenu;
use App\Models\AdminPermission;
use App\Support\Permission\PermissionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testRoutePermission()
    {
        $route = AuthController::class . '@login';
        self::assertNotNull(AdminPermission::query()->where('name', PermissionBuilder::fromRoute($route))->first());
    }

    public function testMenuPermission()
    {
        $menu = AdminMenu::query()->first();
        self::assertNotNull(AdminPermission::query()->where('name', PermissionBuilder::fromMenu($menu))->first());
    }
}
