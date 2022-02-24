<?php

namespace Tests\Feature\Models;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordAttribute()
    {
        $this->seed(AdminSeeder::class);

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
        $this->seed(AdminSeeder::class);

        /** @var Admin $superAdmin */
        $superAdmin = Admin::where('super', true)->first();

        /** @var Admin $commonAdmin */
        $commonAdmin = Admin::where('super', false)->first();

        self::assertFalse($commonAdmin->isSuper());
    }

    public function testPasswordHidden()
    {
        $this->seed(AdminSeeder::class);

        $admin = Admin::first();

        self::assertArrayHasKey('password', $admin);
        self::assertArrayNotHasKey('password', $admin->toArray());

        $admins = Admin::all();

        self::assertArrayHasKey('password', $admins[0]);
        self::assertArrayNotHasKey('password', $admins->toArray()[0]);
    }
}
