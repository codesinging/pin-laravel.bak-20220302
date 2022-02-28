<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->items() as $item) {
            AdminRole::new()->store($item);
        }
    }

    protected function items(): array
    {
        return [
            ['name' => '系统管理员', 'description' => '具有系统管理权限'],
            ['name' => '内容管理员', 'description' => '具有内容管理权限']
        ];
    }
}
