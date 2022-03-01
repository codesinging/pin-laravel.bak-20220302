<?php

namespace Tests\Feature\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Admin;
use App\Models\AdminPermissionPermission;
use App\Models\AdminRole;
use App\Support\Permission\PermissionName;
use App\Support\Routing\RouteParser;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        $superAdmin = $this->admin();
        $this->actingAsAdmin()
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
        $superAdmin = $this->admin();
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

        $permission = PermissionName::fromRoute(AdminController::class.'@update');
        $commonAdmin->givePermissionTo($permission);

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
        $admin = $this->admin();

        $this->actingAs($admin)
            ->getJson('api/admin/admins/' . $admin['id'])
            ->assertOk()
            ->assertJsonPath('data.id', $admin['id'])
            ->assertJsonPath('data.name', $admin['name']);
    }

    public function testDestroy()
    {
        $commonAdmin = $this->commonAdmin();
        $superAdmin = $this->admin();

        // 删除一般管理员
        $this->actingAs($superAdmin)
            ->deleteJson('api/admin/admins/' . $commonAdmin['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertModelMissing($commonAdmin);

        // 超级管理员无法删除

        $this->actingAs($superAdmin)
            ->deleteJson('api/admin/admins/' . $superAdmin['id'])
            ->assertJsonPath('code', ErrorCode::SUPER_ADMIN_DELETE_ERROR)
            ->assertOk();

        self::assertModelExists($superAdmin);
    }

    /**
     * @throws Exception
     */
    public function testGivePermissions()
    {
        $testPermissions = [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($testPermissions as $testPermission) {
            AdminPermissionPermission::create($testPermission);
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
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($testPermissions as $testPermission) {
            AdminPermissionPermission::create($testPermission);
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
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($testPermissions as $testPermission) {
            AdminPermissionPermission::create($testPermission);
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
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($testPermissions as $testPermission) {
            AdminPermissionPermission::create($testPermission);
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

    public function testAssignRoles()
    {
        $roles = [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $admin = $this->admin();

        self::assertFalse($admin->hasAnyRole(['test1', 'test2', 'test3', 'test4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/assign_roles/' . $admin['id'], ['roles' => 'test1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/assign_roles/' . $admin['id'], ['roles' => ['test2', 'test3']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllRoles(['test1', 'test2', 'test3']));
    }

    public function testRemoveRoles()
    {
        $roles = [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $admin = $this->admin();

        self::assertFalse($admin->hasAnyRole(['test1', 'test2', 'test3', 'test4']));

        $admin->assignRole(['test1', 'test3', 'test4']);

        self::assertTrue($admin->hasAllRoles(['test1', 'test3', 'test4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/remove_roles/' . $admin['id'], ['roles' => 'test1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/remove_roles/' . $admin['id'], ['roles' => ['test3', 'test4']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $admin->refresh();

        self::assertFalse($admin->hasAnyRole(['test1', 'test2', 'test3', 'test4']));
    }

    public function testSyncRoles()
    {
        $roles = [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $admin = $this->admin();

        self::assertFalse($admin->hasAnyRole(['test1', 'test2', 'test3', 'test4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/sync_roles/' . $admin['id'], ['roles' => 'test1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasRole('test1'));
        self::assertFalse($admin->hasAnyRole(['test2', 'test3', 'test4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/admins/sync_roles/' . $admin['id'], ['roles' => ['test3', 'test4']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $admin->refresh();

        self::assertTrue($admin->hasAllRoles(['test3', 'test4']));
        self::assertFalse($admin->hasAnyRole(['test1', 'test2']));
    }

    public function testRoles()
    {
        $roles = [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $admin = $this->admin();
        $admin->assignRole(['test3', 'test4']);

        $this->actingAsAdmin()
            ->getJson('api/admin/admins/roles/' . $admin['id'])
            ->assertJsonPath('data.0.role.name', 'test3')
            ->assertJsonPath('data.1.role.name', 'test4')
            ->assertJsonPath('code', 0)
            ->assertOk();
    }
}
