<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role as PermissionRole;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testSeeder()
    {
        $name = '系统管理员';

        $permissionRole = PermissionRole::query()->where('name', $name)->first();
        $role = Role::query()->where('permission_role_id', $permissionRole['id'])->first();

        self::assertNotNull($permissionRole);
        self::assertNotNull($role);
    }
}
