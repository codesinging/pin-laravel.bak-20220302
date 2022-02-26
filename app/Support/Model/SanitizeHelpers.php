<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait SanitizeHelpers
{
    /**
     * 净化数据
     *
     * @param Request|array $request
     *
     * @return array
     */
    public function sanitize(Request|array $request): array
    {
        $data = $request instanceof Request ? $request->all() : $request;
        $fillable = $this->getFillable();

        $fillable = !empty($fillable)
            ? $fillable
            : array_diff(array_diff(Schema::getColumnListing($this->getTable()), $this->getGuarded()), $this->getHidden());

        return array_intersect_key($data, array_flip($fillable));
    }
}
