<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ApiException extends Exception
{
    #[Pure]
    public function __construct(string $message = "", int $code = ErrorCode::ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
