<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Models;

use Spatie\Permission\Models\Permission;

class AdminPermissionPermission extends Permission
{
    protected string $guard_name = 'sanctum';
}
