<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests;

use App\Models\Admin;

trait ActingAsAdmin
{
    protected function actingAsAdmin(): static
    {
        /** @var Admin $admin */
        $admin = (new Admin())->first();

        $this->actingAs($admin);

        return $this;
    }

    protected function superAdmin(): Admin
    {
        return (new Admin())->where('super', true)->first();
    }

    protected function commonAdmin(): Admin
    {
        return (new Admin())->where('super', false)->first();
    }

    protected function actingAsSuperAdmin(): static
    {
        $this->actingAs($this->superAdmin());

        return $this;
    }

    protected function actingAsCommonAdmin(): static
    {
        $this->actingAs($this->commonAdmin());

        return $this;
    }
}
