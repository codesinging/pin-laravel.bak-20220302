<?php

namespace Tests\Feature\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        /** @var Admin $admin */
        $admin = Admin::first();

        $this->actingAs($admin)
            ->getJson('api/admin/admins')
            ->assertOk()
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.data.0.id', 1);
    }

    public function testStoreValidation()
    {
        /** @var Admin $admin */
        $admin = Admin::first();

        $this->actingAs($admin)
            ->postJson('api/admin/admins', [
                'username' => 'admin1'
            ])
            ->assertJsonPath('code', ErrorCode::VALIDATION_ERROR)
            ->assertJsonStructure(['data' => ['name', 'password']])
            ->assertOk();

        $this->actingAs($admin)
            ->postJson('api/admin/admins', [
                'username' => 'admin',
                'name' => 'Admin',
                'password' => 'admin.123'
            ])
            ->assertJsonPath('code', ErrorCode::VALIDATION_ERROR)
            ->assertJsonStructure(['data' => ['name', 'username']])
            ->assertOk();
    }

    public function testStore()
    {
        self::assertCount(2, Admin::all());

        /** @var Admin $admin */
        $admin = Admin::first();

        $this->actingAs($admin)
            ->postJson('api/admin/admins', [
                'username' => 'admin1',
                'name' => 'Admin1',
                'password' => 'admin.123'
            ])
            ->assertOk();

        self::assertCount(3, Admin::all());

        $latestAdmin = Admin::latest('id')->first();

        self::assertEquals('admin1', $latestAdmin['username']);
        self::assertEquals('Admin1', $latestAdmin['name']);
        self::assertTrue(Hash::check('admin.123', $latestAdmin['password']));
    }

    public function testUpdateValidation()
    {
        /** @var Admin $superAdmin */
        $superAdmin = Admin::where('super', true)->first();

        /** @var Admin $commonAdmin */
        $commonAdmin = Admin::where('super', false)->first();

        $this->actingAs($superAdmin)
            ->putJson('api/admin/admins/' . $commonAdmin['id'], [
                'username' => $superAdmin['username'],
                'name' => $superAdmin['name'],
            ])
            ->assertJsonPath('code', ErrorCode::VALIDATION_ERROR)
            ->assertJsonStructure(['data' => ['name', 'username']])
            ->assertOk();
    }

    public function testUpdate()
    {
        /** @var Admin $superAdmin */
        $superAdmin = Admin::where('super', true)->first();

        /** @var Admin $commonAdmin */
        $commonAdmin = Admin::where('super', false)->first();

        // 测试修改登录账号和名称，并未修改密码
        $this->actingAs($superAdmin)
            ->putJson('api/admin/admins/' . $commonAdmin['id'], [
                'username' => 'admin_common',
                'name' => 'Admin_Common',
            ])
            ->assertOk();

        $commonAdmin->refresh();

        self::assertEquals('admin_common', $commonAdmin['username']);
        self::assertEquals('Admin_Common', $commonAdmin['name']);
        self::assertTrue(Hash::check('admin.123', $commonAdmin['password']));

        // 测试修改密码
        $this->actingAs($superAdmin)
            ->putJson('api/admin/admins/' . $commonAdmin['id'], [
                'username' => 'admin_common',
                'name' => 'Admin_Common',
                'password' => 'admin.111'
            ])
            ->assertOk();

        $commonAdmin->refresh();

        self::assertEquals('admin_common', $commonAdmin['username']);
        self::assertEquals('Admin_Common', $commonAdmin['name']);
        self::assertTrue(Hash::check('admin.111', $commonAdmin['password']));

        // 超级管理员可以修改自己的信息
        $this->actingAs($superAdmin)
            ->putJson('api/admin/admins/' . $superAdmin['id'], [
                'username' => 'admin_super',
                'name' => 'Admin_Super',
            ])
            ->assertOk();

        $superAdmin->refresh();

        self::assertEquals('admin_super', $superAdmin['username']);
        self::assertEquals('Admin_Super', $superAdmin['name']);

        // 一般管理员无法修改超级管理员的信息
        $this->actingAs($commonAdmin)
            ->putJson('api/admin/admins/' . $superAdmin['id'], [
                'username' => 'admin_super2',
                'name' => 'Admin_Super2',
            ])
            ->assertJsonPath('code', ErrorCode::SUPER_ADMIN_UPDATE_ERROR)
            ->assertOk();

        $superAdmin->refresh();

        self::assertEquals('admin_super', $superAdmin['username']);
        self::assertEquals('Admin_Super', $superAdmin['name']);
    }

    public function testShow()
    {
        /** @var Admin $admin */
        $admin = Admin::first();

        $this->actingAs($admin)
            ->getJson('api/admin/admins/' . $admin['id'])
            ->assertOk()
            ->assertJsonPath('data.id', $admin['id'])
            ->assertJsonPath('data.name', $admin['name']);
    }

    public function testDestroy()
    {
        /** @var Admin $admin */
        $admin = Admin::where('super', false)->first();

        $this->actingAs($admin)
            ->deleteJson('api/admin/admins/'. $admin['id'])
            ->assertOk();

        self::assertModelMissing($admin);
    }
}
