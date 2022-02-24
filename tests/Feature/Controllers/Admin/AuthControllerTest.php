<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected string $seeder = AdminSeeder::class;

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
        $admin = Admin::first();
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

    public function testUser()
    {
        /** @var Admin $admin */
        $admin = Admin::first();

        $this->actingAs($admin)
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('data.id', $admin['id'])
            ->assertOk();
    }
}
