<?php

namespace Tests\Feature\Database\Seeders;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminRule;
use App\Support\Permission\PermissionName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRuleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testSeeder()
    {
        $name = PermissionName::fromRoute(AuthController::class . '@login');
        $rule = AdminRule::new()->where('name', $name)->first();

        $this->assertNotNull($rule);
        self::assertEquals($name, $rule['name']);
        self::assertEquals('Admin', $rule['module']);
        self::assertEquals('Auth', $rule['controller']);
        self::assertEquals('login', $rule['action']);
        self::assertEquals('管理员认证', $rule['controller_title']);
        self::assertEquals('管理员登录', $rule['action_title']);
    }
}
