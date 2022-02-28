<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use App\Models\AdminPermission;
use App\Models\AdminRule;
use App\Support\Permission\PermissionName;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->routePermission();
        $this->menuPermission();
    }

    /**
     * @return void
     */
    private function routePermission(): void
    {
        $rules = AdminRule::all();

        foreach ($rules as $rule) {
            AdminPermission::findOrCreate($rule['name']);
        }
    }

    /**
     * @return void
     */
    private function menuPermission(): void
    {
        $menus = AdminMenu::all();

        foreach ($menus as $menu) {
            AdminPermission::findOrCreate(PermissionName::fromMenu($menu));
        }
    }
}
