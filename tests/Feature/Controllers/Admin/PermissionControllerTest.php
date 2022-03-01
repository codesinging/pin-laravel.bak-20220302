<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\AdminAction;
use App\Models\AdminMenu;
use App\Models\AdminPermission;
use Database\Seeders\AdminMenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    protected bool $seed = false;

    public function testUpdateAllPermissions()
    {
        self::assertEquals(0, AdminPermission::all()->count());

        $this->seed(AdminMenuSeeder::class);

        $this->seedAdmin()
            ->actingAsAdmin()
            ->getJson('api/admin/permissions/update')
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertGreaterThan(0, AdminPermission::all()->count());
        self::assertGreaterThan(0, AdminAction::all()->count());
        self::assertEquals(AdminAction::all()->count() + AdminMenu::query()->where('status', true)->get()->count(), AdminPermission::all()->count());
    }

    public function testUpdateActionPermissions()
    {
        self::assertEquals(0, AdminPermission::all()->count());

        $this->seedAdmin()
            ->actingAsAdmin()
            ->getJson('api/admin/permissions/update/route')
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertGreaterThan(0, AdminPermission::all()->count());
        self::assertGreaterThan(0, AdminAction::all()->count());
        self::assertEquals(AdminAction::all()->count(), AdminPermission::all()->count());
    }

    public function testUpdateMenuPermissions()
    {
        self::assertEquals(0, AdminPermission::all()->count());

        $this->seed(AdminMenuSeeder::class);

        $this->seedAdmin()
            ->actingAsAdmin()
            ->getJson('api/admin/permissions/update/menu')
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertGreaterThan(0, AdminPermission::all()->count());
        self::assertEquals(AdminMenu::query()->where('status', true)->get()->count(), AdminPermission::all()->count());
    }
}
