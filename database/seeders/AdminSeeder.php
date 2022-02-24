<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    protected array $admins = [
        [
            'username' => 'admin',
            'name' => 'Admin',
            'password' => 'admin.123',
            'super' => true
        ],
        [
            'username' => 'user',
            'name' => 'User',
            'password' => 'admin.123',
            'super' => false
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->admins as $admin) {
            Admin::create($admin);
        }
    }
}
