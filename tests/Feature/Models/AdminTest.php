<?php

namespace Tests\Feature\Models;

use App\Models\Admin;
use App\Models\AdminPermission;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testPasswordAttribute()
    {
        $admin = Admin::first();

        $admin->fill([
            'username' => 'admin222',
        ])->save();

        $admin->refresh();

        self::assertEquals('admin222', $admin['username']);
        self::assertTrue(Hash::check('admin.123', $admin['password']));

        $admin->fill([
            'password' => 'admin.222',
        ])->save();

        $admin->refresh();

        self::assertTrue(Hash::check('admin.222', $admin['password']));
    }

    public function testIsSuper()
    {
        self::assertFalse($this->commonAdmin()->isSuper());
        self::assertTrue($this->admin()->isSuper());
    }

    public function testPasswordHidden()
    {
        $admin = Admin::first();

        self::assertArrayHasKey('password', $admin);
        self::assertArrayNotHasKey('password', $admin->toArray());

        $admins = Admin::all();

        self::assertArrayHasKey('password', $admins[0]);
        self::assertArrayNotHasKey('password', $admins->toArray()[0]);
    }

    /**
     * @throws Exception
     */
    public function testSuperAdmin()
    {
        $commonAdmin = $this->commonAdmin();
        $superAdmin = $this->admin();

        $permissions = ['permission1', 'permission2', 'permission3'];

        foreach ($permissions as $permission) {
            AdminPermission::create(['name' => $permission]);
        }

        foreach ($permissions as $permission) {
            self::assertFalse($commonAdmin->can($permission));
            self::assertTrue($superAdmin->can($permission));
        }
    }
}
