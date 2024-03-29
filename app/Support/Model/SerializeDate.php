<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use DateTimeInterface;

trait SerializeDate
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
