<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRule;
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
        $rules = AdminRule::all();

        foreach ($rules as $rule) {
            AdminPermission::findOrCreate($rule['name']);
        }
    }
}
