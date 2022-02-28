<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Models;

class AdminPermissionRole extends \Spatie\Permission\Models\Role
{
    protected string $guard_name = 'sanctum';
}
