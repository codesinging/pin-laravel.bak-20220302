<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests;

use App\Models\Admin;
use Laravel\Sanctum\Sanctum;

trait ActingAsAdmin
{
    protected function admin(): Admin
    {
        return (new Admin())->where('super', true)->first();
    }

    protected function commonAdmin(): Admin
    {
        return (new Admin())->where('super', false)->first();
    }

    protected function actingAsAdmin(): static
    {
        $this->actingAs($this->admin());
        return $this;
    }

    protected function actingAsCommonAdmin(): static
    {
        $this->actingAs($this->commonAdmin());
        return $this;
    }

}
