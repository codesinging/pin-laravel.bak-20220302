<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => '系统管理员', 'guard_name' => Admin::GUARD]);
        Role::create(['name' => '内容管理员', 'guard_name' => Admin::GUARD]);
    }
}
