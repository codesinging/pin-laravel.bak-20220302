<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role as PermissionRole;

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
            $permissionRole = PermissionRole::create(Arr::only($item, ['name', 'guard_name']));

            $role = Role::create(Arr::only($item, ['description']));
            $role->role()->associate($permissionRole);
            $role->save();
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
