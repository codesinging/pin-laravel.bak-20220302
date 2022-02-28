<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::get('test', [Admin\TestController::class, 'index']);
Route::get('readme', [Admin\ReadmeController::class, 'index']);
Route::post('auth/login', [Admin\AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'admin.authorize'])->group(function () {

    Route::get('auth/user', [Admin\AuthController::class, 'user']);

    Route::get('admins/permissions/{admin}', [Admin\AdminController::class, 'permissions']);
    Route::post('admins/give_permissions/{admin}', [Admin\AdminController::class, 'givePermissions']);
    Route::post('admins/revoke_permissions/{admin}', [Admin\AdminController::class, 'revokePermissions']);
    Route::post('admins/sync_permissions/{admin}', [Admin\AdminController::class, 'syncPermissions']);
    Route::get('admins/roles/{admin}', [Admin\AdminController::class, 'roles']);
    Route::post('admins/assign_roles/{admin}', [Admin\AdminController::class, 'assignRoles']);
    Route::post('admins/remove_roles/{admin}', [Admin\AdminController::class, 'removeRoles']);
    Route::post('admins/sync_roles/{admin}', [Admin\AdminController::class, 'syncRoles']);
    Route::apiResource('admins', Admin\AdminController::class);

    Route::get('roles/permissions/{role}', [Admin\RoleController::class, 'permissions']);
    Route::post('roles/give_permissions/{role}', [Admin\RoleController::class, 'givePermissions']);
    Route::post('roles/revoke_permissions/{role}', [Admin\RoleController::class, 'revokePermissions']);
    Route::post('roles/sync_permissions/{role}', [Admin\RoleController::class, 'syncPermissions']);
    Route::apiResource('roles', Admin\RoleController::class);

    Route::apiResource('menus', Admin\MenuController::class);

    Route::get('rules', [Admin\RuleController::class, 'index']);

});
