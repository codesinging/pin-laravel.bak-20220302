<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\AdminPermissionPermission;
use App\Models\AdminPermissionRole;
use App\Models\AdminRole;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testIndex()
    {
        $this->actingAsAdmin()
            ->get('api/admin/roles')
            ->assertJsonPath('data.data.0.id', 1)
            ->assertJsonPath('data.data.0.role.id', 1)
            ->assertOk();
    }

    public function testStore()
    {
        $data = [
            'name' => '测试角色',
            'description' => '测试角色的说明'
        ];
        $this->actingAsAdmin()
            ->postJson('api/admin/roles', $data)
            ->assertJsonPath('code', 0)
            ->assertOk();

        $role = AdminRole::query()->latest('id')->first();

        self::assertEquals($data['description'], $role['description']);
        self::assertEquals($data['name'], $role['role']['name']);
        self::assertEquals($role['permission_role_id'], $role['role']['id']);
    }

    public function testUpdate()
    {
        $data = [
            'name' => '测试角色',
            'description' => '测试角色的说明'
        ];

        $role = AdminRole::first();

        $this->actingAsAdmin()
            ->putJson('api/admin/roles/' . $role['id'], $data)
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.description', $data['description'])
            ->assertJsonPath('data.role.name', $data['name'])
            ->assertOk();
    }

    public function testShow()
    {
        $role = AdminRole::new()->first();

        $this->actingAsAdmin()
            ->getJson('api/admin/roles/' . $role['id'])
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.id', $role['id'])
            ->assertJsonPath('data.role.id', $role['role']['id'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $role = AdminRole::first();

        $this->actingAsAdmin()
            ->deleteJson('api/admin/roles/' . $role['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertNull(AdminRole::find($role['id']));
        self::assertNull(AdminPermissionRole::find($role['permission_role_id']));
    }

    /**
     * @throws Exception
     */
    public function testGivePermissions()
    {
        $roles = [
            ['name' => 'role1'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $permissions = [
            ['name' => 'permission1'],
            ['name' => 'permission2'],
            ['name' => 'permission3'],
            ['name' => 'permission4'],
        ];

        foreach ($permissions as $permission) {
            AdminPermissionPermission::create($permission);
        }

        $permissionRole1 = AdminPermissionRole::findByName('role1');
        $role1 = AdminRole::query()->where('permission_role_id', $permissionRole1['id'])->first();

        self::assertFalse($permissionRole1->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/roles/give_permissions/' . $role1['id'], ['permissions' => 'permission1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsAdmin()
            ->postJson('api/admin/roles/give_permissions/' . $role1['id'], ['permissions' => ['permission2', 'permission3']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $permissionRole1->refresh();
        self::assertTrue($permissionRole1->hasAllPermissions('permission1', 'permission2', 'permission3'));
    }

    /**
     * @throws Exception
     */
    public function testRevokePermissions()
    {
        $roles = [
            ['name' => 'role1'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $permissions = [
            ['name' => 'permission1'],
            ['name' => 'permission2'],
            ['name' => 'permission3'],
            ['name' => 'permission4'],
        ];

        foreach ($permissions as $permission) {
            AdminPermissionPermission::create($permission);
        }

        $permissionRole1 = AdminPermissionRole::findByName('role1');
        $role1 = AdminRole::query()->where('permission_role_id', $permissionRole1['id'])->first();

        self::assertFalse($permissionRole1->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));

        $permissionRole1->givePermissionTo(['permission1', 'permission3']);

        self::assertTrue($permissionRole1->hasAllPermissions(['permission1', 'permission3']));

        $this->actingAsAdmin()
            ->postJson('api/admin/roles/revoke_permissions/' . $role1['id'], ['permissions' => 'permission1'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsAdmin()
            ->postJson('api/admin/roles/revoke_permissions/' . $role1['id'], ['permissions' => ['permission3']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $permissionRole1->refresh();
        self::assertFalse($permissionRole1->hasAnyPermission('permission1', 'permission2', 'permission3', 'permission4'));
    }

    /**
     * @throws Exception
     */
    public function testSyncPermissions()
    {
        $roles = [
            ['name' => 'role1'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $permissions = [
            ['name' => 'permission1'],
            ['name' => 'permission2'],
            ['name' => 'permission3'],
            ['name' => 'permission4'],
        ];

        foreach ($permissions as $permission) {
            AdminPermissionPermission::create($permission);
        }

        $permissionRole1 = AdminPermissionRole::findByName('role1');
        $role1 = AdminRole::query()->where('permission_role_id', $permissionRole1['id'])->first();

        self::assertFalse($permissionRole1->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));

        $permissionRole1->givePermissionTo(['permission1', 'permission3']);

        self::assertTrue($permissionRole1->hasAllPermissions(['permission1', 'permission3']));

        $this->actingAsAdmin()
            ->postJson('api/admin/roles/sync_permissions/' . $role1['id'], ['permissions' => 'permission2'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $permissionRole1->refresh();
        self::assertTrue($permissionRole1->hasAllPermissions('permission2'));
        self::assertFalse($permissionRole1->hasAnyPermission(['permission1', 'permission3', 'permission4']));

        $this->actingAsAdmin()
            ->postJson('api/admin/roles/sync_permissions/' . $role1['id'], ['permissions' => ['permission3', 'permission4']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $permissionRole1->refresh();
        self::assertTrue($permissionRole1->hasAllPermissions('permission3', 'permission4'));
        self::assertFalse($permissionRole1->hasAnyPermission(['permission1', 'permission2']));
    }

    /**
     * @throws Exception
     */
    public function testPermissions()
    {
        $roles = [
            ['name' => 'role1'],
        ];

        foreach ($roles as $role) {
            (new AdminRole())->store($role);
        }

        $permissions = [
            ['name' => 'permission1'],
            ['name' => 'permission2'],
            ['name' => 'permission3'],
            ['name' => 'permission4'],
        ];

        foreach ($permissions as $permission) {
            AdminPermissionPermission::create($permission);
        }

        $permissionRole1 = AdminPermissionRole::findByName('role1');
        $role1 = AdminRole::query()->where('permission_role_id', $permissionRole1['id'])->first();

        self::assertFalse($permissionRole1->hasAnyPermission(['permission1', 'permission2', 'permission3', 'permission4']));

        $permissionRole1->givePermissionTo(['permission1', 'permission3']);

        self::assertTrue($permissionRole1->hasAllPermissions(['permission1', 'permission3']));

        $this->actingAsAdmin()
            ->get('api/admin/roles/permissions/' . $role1['id'])
            ->assertJsonPath('data.0.name', 'permission1')
            ->assertJsonPath('data.1.name', 'permission3')
            ->assertJsonPath('code', 0)
            ->assertOk();

    }
}
