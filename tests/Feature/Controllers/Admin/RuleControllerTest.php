<?php

namespace Tests\Feature\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class RuleControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testIndex()
    {
        $this->actingAsAdmin()
            ->get('api/admin/rules')
            ->assertOk();
    }
}
