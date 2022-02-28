<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\AdminPermissionRole;
use App\Models\AdminRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminRoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testSeeder()
    {
        $name = '系统管理员';

        $adminRole = AdminPermissionRole::query()->where('name', $name)->first();
        $role = AdminRole::query()->where('permission_role_id', $adminRole['id'])->first();

        self::assertNotNull($adminRole);
        self::assertNotNull($role);
    }
}
