<?php

namespace Tests\Feature\Database\Seeders;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminMenu;
use App\Models\AdminPermissionPermission;
use App\Support\Permission\PermissionName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testRoutePermission()
    {
        $route = AuthController::class . '@login';
        self::assertNotNull(AdminPermissionPermission::query()->where('name', PermissionName::fromRoute($route))->first());
    }

    public function testMenuPermission()
    {
        $menu = AdminMenu::query()->first();
        self::assertNotNull(AdminPermissionPermission::query()->where('name', PermissionName::fromMenu($menu))->first());
    }
}
