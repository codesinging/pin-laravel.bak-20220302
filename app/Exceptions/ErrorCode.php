<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

class ErrorCode
{
    const OK = 0;

    const ERROR = -1;

    const VALIDATION_ERROR = 90100;

    const SUPER_ADMIN_UPDATE_ERROR = 90200;
    const SUPER_ADMIN_DELETE_ERROR = 90201;

    const AUTH_USER_NOT_EXISTED = 90300;
    const AUTH_PASSWORD_NOT_MATCHED = 90301;
    const AUTH_USER_STATUS_ERROR = 90302;

    const PERMISSION_NO_AUTHORIZATION = 90401;
}
