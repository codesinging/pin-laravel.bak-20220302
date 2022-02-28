<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

trait IsSuper
{
    /**
     * 是否超级管理员
     *
     * @return bool
     */
    public function isSuper(): bool
    {
        return (boolean)($this->attributes['super'] ?? false);
    }

}
