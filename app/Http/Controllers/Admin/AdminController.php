<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @title 管理员管理
 */
class AdminController extends Controller
{
    /**
     * @title 获取管理员列表
     *
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function index(Admin $admin): JsonResponse
    {
        $lister = $admin->lister();
        return $this->success($lister);
    }

    /**
     * @title 新增管理员
     *
     * @param StoreAdminRequest $request
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function store(StoreAdminRequest $request, Admin $admin): JsonResponse
    {
        return $admin->fill($request->all())->save()
            ? $this->success('新增成功')
            : $this->error('新增失败');
    }

    /**
     * @title 更新管理员
     *
     * @param UpdateAdminRequest $request
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function update(UpdateAdminRequest $request, Admin $admin): JsonResponse
    {
        if ($admin->isSuper() && $request->user()['id'] !== $admin['id']) {
            return $this->error('无权限', ErrorCode::SUPER_ADMIN_UPDATE_ERROR);
        }

        $request->validate([
            'name' => Rule::unique('admins')->ignore($admin),
            'username' => Rule::unique('admins')->ignore($admin),
        ], [], $request->attributes());

        return $admin->fill($request->all())->save()
            ? $this->success('更新成功')
            : $this->error('更新失败');
    }

    /**
     * 查看管理员详情
     *
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function show(Admin $admin): JsonResponse
    {
        return $this->success($admin);
    }

    /**
     * 删除管理员
     *
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function destroy(Admin $admin): JsonResponse
    {
        if ($admin->isSuper()) {
            return $this->error('超级管理员无法删除', ErrorCode::SUPER_ADMIN_DELETE_ERROR);
        }

        return $admin->delete()
            ? $this->success('删除成功')
            : $this->error('删除失败');
    }

    /**
     * @title 分配权限
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function givePermissions(Admin $admin, Request $request): JsonResponse
    {
        $admin->givePermissionTo(Arr::wrap($request->get('permissions')));
        return $this->success('新增权限成功');
    }

    /**
     * @title 移除权限
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function revokePermissions(Admin $admin, Request $request): JsonResponse
    {
        $admin->revokePermissionTo(Arr::wrap($request->get('permissions')));
        return $this->success('移除权限成功');
    }

    /**
     * @title 设置权限
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function syncPermissions(Admin $admin, Request $request): JsonResponse
    {
        $admin->syncPermissions(Arr::wrap($request->get('permissions')));
        return $this->success('设置权限成功');
    }

    /**
     * @title 获取所有权限
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function permissions(Admin $admin, Request $request): JsonResponse
    {
        $from = $request->get('from', 'all');

        $permissions = match ($from) {
            'direct' => $admin->getDirectPermissions(),
            'role' => $admin->getPermissionsViaRoles(),
            default => $admin->getAllPermissions(),
        };

        return $this->success('获取权限成功', $permissions->toArray());
    }

    /**
     * @title 指派角色
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function assignRoles(Admin $admin, Request $request): JsonResponse
    {
        $admin->assignRole(Arr::wrap($request->get('roles')));
        return $this->success('指派角色成功');
    }

    /**
     * @title 删除角色
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeRoles(Admin $admin, Request $request): JsonResponse
    {
        foreach (Arr::wrap($request->get('roles')) as $role) {
            $admin->removeRole($role);
        }
        return $this->success('删除角色成功');
    }

    /**
     * @title 设置角色
     *
     * @param Admin $admin
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function syncRoles(Admin $admin, Request $request): JsonResponse
    {
        $admin->syncRoles(Arr::wrap($request->get('roles')));
        return $this->success('设置角色成功');
    }

    /**
     * @title 获取所有角色
     *
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function roles(Admin $admin): JsonResponse
    {
        $roles = $admin->getRoles()->toArray();
        return $this->success('获取所有角色成功', $roles);
    }
}
