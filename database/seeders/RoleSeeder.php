<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->items() as $item) {
            (new Role())->store($item);
        }
    }

    protected function items(): array
    {
        return [
            ['name' => '系统管理员', 'guard_name' => Admin::GUARD, 'description' => '具有系统管理权限'],
            ['name' => '内容管理员', 'guard_name' => Admin::GUARD, 'description' => '具有内容管理权限']
        ];
    }
}
