<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::get('test', fn() => 'admin');

Route::apiResource('admins', Admin\AdminsController::class);
