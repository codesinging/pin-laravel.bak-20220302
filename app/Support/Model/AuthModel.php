<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class AuthModel extends User
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;
    use ListHelpers;
    use SerializeDate;
    use SanitizeHelpers;
    use InstanceHelpers;
}
