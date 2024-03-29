<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Http\Controllers\Admin\AuthController;
use App\Models\Admin;
use App\Models\AdminMenu;
use App\Support\Permission\PermissionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testLogin()
    {
        // 测试账号和密码验证
        $this->postJson('api/admin/auth/login', [])
            ->assertOk()
            ->assertJsonPath('code', ErrorCode::VALIDATION_ERROR)
            ->assertJsonStructure(['data' => ['username', 'password']]);

        // 测试账号不存在
        $this->postJson('api/admin/auth/login', ['username' => 'not_existed', 'password' => 'admin.123'])
            ->assertOk()
            ->assertJsonPath('code', ErrorCode::AUTH_USER_NOT_EXISTED);

        // 测试账号密码不匹配
        $this->postJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.111'])
            ->assertOk()
            ->assertJsonPath('code', ErrorCode::AUTH_PASSWORD_NOT_MATCHED);

        // 测试账号状态异常
        $admin = $this->admin();
        $admin->update(['status' => false]);

        $this->postJson('api/admin/auth/login', ['username' => $admin['username'], 'password' => 'admin.123'])
            ->assertOk()
            ->assertJsonPath('code', ErrorCode::AUTH_USER_STATUS_ERROR);

        $admin->update(['status' => true]);

        // 测试认证成功
        $this->postJson('api/admin/auth/login', ['username' => 'admin', 'password' => 'admin.123'])
            ->assertOk()
            ->assertJsonPath('code', ErrorCode::OK)
            ->assertJsonStructure(['code', 'data' => ['admin', 'token']])
            ->assertJsonPath('data.admin.username', 'admin');
    }

    public function testLogout()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/auth/logout')
            ->assertOk();
    }

    public function testUser()
    {
        $admin = $this->admin();
        $this->actingAsAdmin()
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('data.id', $admin['id'])
            ->assertOk();
    }

    public function testMenus()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/auth/menus')
            ->assertJsonStructure(['data' => [['id', 'name']]])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $commonAdmin = $this->commonAdmin();

        $commonAdmin->givePermissionTo(PermissionBuilder::fromRoute(AuthController::class . '@menus'));

        $this->actingAsCommonAdmin()
            ->getJson('api/admin/auth/menus')
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('code', 0)
            ->assertOk();

        $menu = AdminMenu::query()->first();

        $commonAdmin->givePermissionTo(PermissionBuilder::fromMenu($menu));

        $this->actingAsCommonAdmin()
            ->getJson('api/admin/auth/menus')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $menu['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();
    }
}
