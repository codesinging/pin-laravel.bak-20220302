<?php

namespace Database\Seeders;

use App\Support\Permission\PermissionUpdater;
use Illuminate\Database\Seeder;
use ReflectionException;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run()
    {
        PermissionUpdater::updateAdminPermissions();
    }
}
