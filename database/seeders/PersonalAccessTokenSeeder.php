<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1|fo2VaWmq37vm12gb0CfANjrEZ8hDecLRdfoXQSsJ
        PersonalAccessToken::query()->create([
            'tokenable_type' => 'App\Models\Admin',
            'tokenable_id' => 1,
            'name' => '',
            'token' => '63c60463dbed8753ad859ecd15985b71a9b8a4d1f3d70a79f62db6e025aac1af',
            'abilities' => ["*"]
        ]);
    }
}
