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
}
