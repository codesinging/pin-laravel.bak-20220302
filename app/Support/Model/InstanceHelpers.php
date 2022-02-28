<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use JetBrains\PhpStorm\Pure;

trait InstanceHelpers
{
    #[Pure]
    public static function new(array $attributes = []): static
    {
        return new static($attributes);
    }
}
