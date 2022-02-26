<?php

namespace Tests\Feature\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class AuthRuleControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testIndex()
    {
        $this->actingAsAdmin()
            ->get('api/admin/auth_rules')
            ->assertOk();
    }
}
