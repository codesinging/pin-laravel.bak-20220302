<?php

namespace Tests\Feature\Middleware;

use App\Exceptions\ErrorCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class AdminAuthorizeTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testUnauthorizedRoute()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/readme')
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsCommonAdmin()
            ->getJson('api/admin/readme')
            ->assertJsonPath('code', 0)
            ->assertOk();
    }

    public function testCan()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsCommonAdmin()
            ->getJson('api/admin/auth/user')
            ->assertJsonPath('code', ErrorCode::PERMISSION_NO_AUTHORIZATION)
            ->assertOk();
    }
}
