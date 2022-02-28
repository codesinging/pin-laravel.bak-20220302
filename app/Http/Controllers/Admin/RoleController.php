<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
     *
     * @return JsonResponse
     */
    public function store(RoleRequest $request, Role $role): JsonResponse
    {
        $role = $role->store($request->all(), Admin::GUARD);

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
        $role = $role->sync($role, $request->all(), Admin::GUARD);

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

    /**
     * @title 分配权限
     *
     * @param Role $role
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function givePermissions(Role $role, Request $request): JsonResponse
    {
        $role->permissionRole()->givePermissionTo(Arr::wrap($request->get('permissions')));
        return $this->success('分配权限成功');
    }

    /**
     * @title 移除权限
     *
     * @param Role $role
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function revokePermissions(Role $role, Request $request): JsonResponse
    {
        $role->permissionRole()->revokePermissionTo(Arr::wrap($request->get('permissions')));
        return $this->success('移除权限成功');
    }

    /**
     * @title 设置权限
     *
     * @param Role $role
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function syncPermissions(Role $role, Request $request): JsonResponse
    {
        $role->permissionRole()->syncPermissions(Arr::wrap($request->get('permissions')));
        return $this->success('设置权限成功');
    }

    /**
     * @title 获取权限
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function permissions(Role $role): JsonResponse
    {
        $permissions = $role->permissionRole()->getAllPermissions();

        return $this->success('获取权限成功', $permissions->toArray());
    }
}
