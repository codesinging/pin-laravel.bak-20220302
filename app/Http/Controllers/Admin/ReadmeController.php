<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;

class ReadmeController extends Controller
{
    public function index(): JsonResponse
    {
        $version = '1.0.0';
        return $this->success(compact('version'));
    }
}
