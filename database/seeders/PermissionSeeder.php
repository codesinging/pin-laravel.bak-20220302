<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Rule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rules = Rule::all();

        foreach ($rules as $rule) {
            Permission::findOrCreate($rule['name'], $rule['guard']);
        }
    }
}
