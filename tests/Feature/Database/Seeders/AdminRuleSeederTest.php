<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\AdminRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminRuleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function testSeeder()
    {
        $name = 'route:Admin/Auth@login';
        $rule = AdminRule::new()->where('name', $name)->first();

        $this->assertNotNull($rule);
        self::assertEquals($name, $rule['name']);
        self::assertEquals('route', $rule['type']);
        self::assertEquals('Admin', $rule['module']);
        self::assertEquals('Auth', $rule['controller']);
        self::assertEquals('login', $rule['action']);
        self::assertEquals('管理员认证', $rule['controller_title']);
        self::assertEquals('管理员登录', $rule['action_title']);
    }
}
