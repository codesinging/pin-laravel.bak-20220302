<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsAdmin;
use Tests\TestCase;
use Spatie\Permission\Models\Role as PermissionRole;

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

        $role = Role::query()->latest('id')->first();

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

        $role = Role::first();

        $this->actingAsAdmin()
            ->putJson('api/admin/roles/'.$role['id'], $data)
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.description', $data['description'])
            ->assertJsonPath('data.role.name', $data['name'])
            ->assertOk();
    }

    public function testShow()
    {
        $role = Role::first();

        $this->actingAsAdmin()
            ->getJson('api/admin/roles/'. $role['id'])
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.id', $role['id'])
            ->assertJsonPath('data.role.id', $role['role']['id'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $role = Role::first();

        $this->actingAsAdmin()
            ->deleteJson('api/admin/roles/'. $role['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        self::assertNull(Role::find($role['id']));
        self::assertNull(PermissionRole::find($role['permission_role_id']));
    }
}
