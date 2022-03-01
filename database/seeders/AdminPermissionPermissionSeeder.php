<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use App\Models\AdminPermissionPermission;
use App\Models\AdminPermission;
use App\Support\Permission\PermissionName;
use Illuminate\Database\Seeder;

class AdminPermissionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->adminRoutePermission();
        $this->adminMenuPermission();
    }

    /**
     * @return void
     */
    private function adminRoutePermission(): void
    {
        $rules = AdminPermission::all();

        foreach ($rules as $rule) {
            AdminPermissionPermission::findOrCreate($rule['name']);
        }
    }

    /**
     * @return void
     */
    private function adminMenuPermission(): void
    {
        $menus = AdminMenu::all();

        foreach ($menus as $menu) {
            AdminPermissionPermission::findOrCreate(PermissionName::fromMenu($menu));
        }
    }
}
