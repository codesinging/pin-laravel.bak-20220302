<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Models\Admin;
use App\Models\AdminMenu;
use App\Support\Model\AuthModel;
use App\Support\Permission\PermissionBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @title 管理员认证
 */
class AuthController extends Controller
{
    /**
     * @title 管理员登录
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username' => '登录账号不能为空',
            'password' => '登录密码不能为空'
        ]);

        /** @var Admin $admin */
        $admin = Admin::where('username', $request->get('username'))->first();

        if (!$admin) {
            return $this->error('账号不存在', ErrorCode::AUTH_USER_NOT_EXISTED);
        }

        if (!Hash::check($request->get('password'), $admin['password'])) {
            return $this->error('账号密码不匹配', ErrorCode::AUTH_PASSWORD_NOT_MATCHED);
        }

        if (!$admin['status']) {
            return $this->error('账号状态异常', ErrorCode::AUTH_USER_STATUS_ERROR);
        }

        $token = $admin->createToken('admin_' . $admin['id'])->plainTextToken;

        return $this->success('登录成功', compact('admin', 'token'));
    }

    /**
     * @title 注销登录
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if (method_exists($token = $request->user()->currentAccessToken(), 'delete')){
            $token->delete();
        }
        return $this->success('注销登录成功');
    }

    /**
     * @title 获取认证管理员
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return Auth::check()
            ? $this->success('获取认证用户成功', $request->user())
            : $this->error('获取认证用户失败');
    }

    /**
     * @title 获取菜单
     *
     * @param AdminMenu $menu
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function menus(AdminMenu $menu, Request $request): JsonResponse
    {
        $menu = $menu->where('status', true)->orderByDesc('sort');

        /** @var Admin $admin */
        $admin = $request->user();

        if (!$admin->isSuper()) {
            $menuIds = $admin->getAllPermissions()
                ->filter(fn($permission) => PermissionBuilder::isMenu($permission['name']))
                ->map(fn($permission) => PermissionBuilder::menuId($permission['name']))
                ->toArray();

            $menu = $menu->whereIn('id', $menuIds);
        }

        $menus = $menu->get()->toArray();

        return $this->success('获取菜单成功', $menus);
    }
}
