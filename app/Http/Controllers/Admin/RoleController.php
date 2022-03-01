<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleRequest;
use App\Models\AdminRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @title 管理员角色管理
 */
class RoleController extends Controller
{
    /**
     * @title 获取管理员角色列表
     *
     * @param AdminRole $role
     *
     * @return JsonResponse
     */
    public function index(AdminRole $role): JsonResponse
    {
        $lister = $role->lister(function (Builder $builder) {
            $builder->orderByDesc('sort');
        });

        return $this->success('获取角色列表成功', $lister);
    }

    /**
     * @title 新增管理员角色
     *
     * @param RoleRequest $request
     * @param AdminRole $role
     *
     * @return JsonResponse
     */
    public function store(RoleRequest $request, AdminRole $role): JsonResponse
    {
        $role = $role->store($request->all());

        return $this->success('新增角色成功', $role);
    }

    /**
     * @title 更新管理员角色
     *
     * @param RoleRequest $request
     * @param AdminRole $role
     *
     * @return JsonResponse
     */
    public function update(RoleRequest $request, AdminRole $role): JsonResponse
    {
        $role = $role->sync($role, $request->all());

        return $this->success('更新角色成功', $role);
    }

    /**
     * @title 获取管理员角色详情
     *
     * @param AdminRole $role
     *
     * @return JsonResponse
     */
    public function show(AdminRole $role): JsonResponse
    {
        return $this->success('获取角色详情成功', $role);
    }

    /**
     * @title 删除管理员角色
     *
     * @param AdminRole $role
     *
     * @return JsonResponse
     */
    public function destroy(AdminRole $role): JsonResponse
    {
        $role->role()->delete();
        $role->delete();
        return $this->success('删除角色成功');
    }

    /**
     * @title 分配管理员角色权限
     *
     * @param AdminRole $role
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function givePermissions(AdminRole $role, Request $request): JsonResponse
    {
        $role->relatedRole()->givePermissionTo(Arr::wrap($request->get('permissions')));
        return $this->success('分配权限成功');
    }

    /**
     * @title 移除管理员角色权限
     *
     * @param AdminRole $role
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function revokePermissions(AdminRole $role, Request $request): JsonResponse
    {
        $role->relatedRole()->revokePermissionTo(Arr::wrap($request->get('permissions')));
        return $this->success('移除权限成功');
    }

    /**
     * @title 设置管理员角色权限
     *
     * @param AdminRole $role
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function syncPermissions(AdminRole $role, Request $request): JsonResponse
    {
        $role->relatedRole()->syncPermissions(Arr::wrap($request->get('permissions')));
        return $this->success('设置权限成功');
    }

    /**
     * @title 获取管理员角色权限
     *
     * @param AdminRole $role
     *
     * @return JsonResponse
     */
    public function permissions(AdminRole $role): JsonResponse
    {
        $permissions = $role->relatedRole()->getAllPermissions();

        return $this->success('获取权限成功', $permissions->toArray());
    }
}
