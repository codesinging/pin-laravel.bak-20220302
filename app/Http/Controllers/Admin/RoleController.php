<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role as PermissionRole;

/**
 * @title 角色管理
 */
class RoleController extends Controller
{
    /**
     * @title 获取角色列表
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function index(Role $role): JsonResponse
    {
        $lister = $role->lister(function (Builder $builder) {
            $builder->orderByDesc('sort');
        });

        return $this->success('获取角色列表成功', $lister);
    }

    /**
     * @title 新增角色
     *
     * @param RoleRequest $request
     * @param Role $role
     * @param PermissionRole $permissionRole
     *
     * @return JsonResponse
     */
    public function store(RoleRequest $request, Role $role, PermissionRole $permissionRole): JsonResponse
    {
        $permissionRole->fill(array_merge($request->only(['name']), ['guard_name' => Admin::GUARD]))->save();
        $role->fill($role->sanitize($request));
        $role->role()->associate($permissionRole);
        $role->save();

        return $this->success('新增角色成功', $role);
    }

    /**
     * @title 更新角色
     *
     * @param RoleRequest $request
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        $role->fill($role->sanitize($request))->save();
        $role->role()->update($request->only(['name']));
        $role->refresh();

        return $this->success('更新角色成功', $role);
    }

    /**
     * @title 获取角色详情
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return $this->success('获取角色详情成功', $role);
    }

    /**
     * @title 删除角色
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->role()->delete();
        $role->delete();
        return $this->success('删除角色成功');
    }
}
