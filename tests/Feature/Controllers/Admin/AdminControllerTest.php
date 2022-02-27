<?php

namespace Tests\Feature\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Models\Admin;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testIndex()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/admins')
            ->assertOk()
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.data.0.id', 1);
    }

    public function testStoreValidation()
    {
        $this->actingAsAdmin()
            ->postJson('api/admin/admins', [
                'username' => 'admin1'
            ])
            ->assertJsonPath('code', ErrorCode::VALIDATION_ERROR)
            ->assertJsonStructure(['data' => ['name', 'password']])
            ->assertOk();

        $this->actingAsAdmin()
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

        $this->actingAsAdmin()
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
        $commonAdmin = $this->commonAdmin();
        $superAdmin = $this->superAdmin();
        $this->actingAsSuperAdmin()
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
        $superAdmin = $this->superAdmin();
        $commonAdmin = $this->commonAdmin();

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
        $admin = $this->commonAdmin();

        $this->actingAs($admin)
            ->getJson('api/admin/admins/' . $admin['id'])
            ->assertOk()
            ->assertJsonPath('data.id', $admin['id'])
            ->assertJsonPath('data.name', $admin['name']);
    }

    public function testDestroy()
    {
        // 删除一般管理员
        $admin = $this->commonAdmin();

        $this->actingAs($admin)
            ->deleteJson('api/admin/admins/' . $admin['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertModelMissing($admin);

        // 超级管理员无法删除
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->deleteJson('api/admin/admins/' . $admin['id'])
            ->assertJsonPath('code', ErrorCode::SUPER_ADMIN_DELETE_ERROR)
            ->assertOk();

        self::assertModelExists($admin);
    }

    /**
     * @throws Exception
     */
    public function testGivePermissions()
    {
        $testPermissions = [
            ['name' => 'test1', 'guard_name' => 'sanctum'],
            ['name' => 'test2', 'guard_name' => 'sanctum'],
            ['name' => 'test3', 'guard_name' => 'sanctum'],
            ['name' => 'test4', 'guard_name' => 'sanctum'],
        ];

        foreach ($testPermissions as $testPermission) {
            Permission::create($testPermission);
        }

        $commonAdmin = $this->commonAdmin();

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/give_permissions/' . $commonAdmin['id'], ['permissions' => 'test1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/give_permissions/' . $commonAdmin['id'], ['permissions' => ['test2', 'test3']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $commonAdmin->refresh();

        self::assertTrue($commonAdmin->hasAllPermissions(['test1', 'test2', 'test3']));

        self::assertFalse($commonAdmin->hasPermissionTo('test4'));
    }

    /**
     * @throws Exception
     */
    public function testRevokePermissions()
    {
        $testPermissions = [
            ['name' => 'test1', 'guard_name' => 'sanctum'],
            ['name' => 'test2', 'guard_name' => 'sanctum'],
            ['name' => 'test3', 'guard_name' => 'sanctum'],
            ['name' => 'test4', 'guard_name' => 'sanctum'],
        ];

        foreach ($testPermissions as $testPermission) {
            Permission::create($testPermission);
        }

        $admin = $this->admin();

        $admin->givePermissionTo(['test1', 'test2', 'test3']);

        self::assertTrue($admin->hasAllPermissions(['test1', 'test2', 'test3']));
        self::assertFalse($admin->hasPermissionTo('test4'));

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/revoke_permissions/' . $admin['id'], ['permissions' => 'test1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/revoke_permissions/' . $admin['id'], ['permissions' => ['test2', 'test3']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $admin->refresh();

        self::assertFalse($admin->hasAnyPermission(['test1', 'test2', 'test3', 'test4']));
    }

    /**
     * @throws Exception
     */
    public function testSyncPermissions()
    {
        $testPermissions = [
            ['name' => 'test1', 'guard_name' => 'sanctum'],
            ['name' => 'test2', 'guard_name' => 'sanctum'],
            ['name' => 'test3', 'guard_name' => 'sanctum'],
            ['name' => 'test4', 'guard_name' => 'sanctum'],
        ];

        foreach ($testPermissions as $testPermission) {
            Permission::create($testPermission);
        }

        $admin = $this->admin();

        $admin->givePermissionTo(['test1', 'test3']);

        self::assertTrue($admin->hasAllPermissions(['test1', 'test3']));
        self::assertFalse($admin->hasAnyPermission(['test2', 'test4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/sync_permissions/' . $admin['id'], ['permissions' => ['test2', 'test3']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllPermissions(['test2', 'test3']));
        self::assertFalse($admin->hasAnyPermission(['test1', 'test4']));
    }

    public function testPermissions()
    {
        $testPermissions = [
            ['name' => 'test1', 'guard_name' => 'sanctum'],
            ['name' => 'test2', 'guard_name' => 'sanctum'],
            ['name' => 'test3', 'guard_name' => 'sanctum'],
            ['name' => 'test4', 'guard_name' => 'sanctum'],
        ];

        foreach ($testPermissions as $testPermission) {
            Permission::create($testPermission);
        }

        $admin = $this->admin();

        $admin->givePermissionTo(['test1', 'test3']);

        $this->actingAsAdmin()
            ->getJson('api/admin/admins/permissions/' . $admin['id'])
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.0.name', 'test1')
            ->assertJsonPath('data.1.name', 'test3')
            ->assertOk();
    }
}
