<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::get('test', fn() => 'admin');

Route::post('auth/login', [Admin\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('auth/user', [Admin\AuthController::class, 'user']);

    Route::apiResource('admins', Admin\AdminsController::class);

});
