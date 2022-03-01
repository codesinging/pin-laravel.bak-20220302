<?php

namespace Tests\Feature\Database\Seeders;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminAction;
use App\Models\AdminPermission;
use App\Support\Permission\PermissionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testSeeder()
    {
        $name = PermissionBuilder::fromRoute(AuthController::class . '@login');
        $permission = AdminPermission::findByName($name);
        $action = AdminAction::query()->where('permission_id', $permission['id'])->first();
        $this->assertNotNull($permission);
        $this->assertNotNull($action);

        self::assertEquals($name, $permission['name']);
        self::assertEquals('Admin/Auth', $action['controller']);
        self::assertEquals('login', $action['action']);
        self::assertEquals('管理员认证', $action['controller_title']);
        self::assertEquals('管理员登录', $action['action_title']);
    }
}
