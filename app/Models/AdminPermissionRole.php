<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Models;

use Spatie\Permission\Models\Role;

class AdminPermissionRole extends Role
{
    protected string $guard_name = 'sanctum';
}
