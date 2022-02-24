<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MenuRequest;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

/**
 * @title 后台菜单管理
 */
class MenuController extends Controller
{
    /**
     * @title 获取菜单列表
     *
     * @param Menu $menu
     *
     * @return JsonResponse
     */
    public function index(Menu $menu): JsonResponse
    {
        $menus = $menu->orderByDesc('sort')->get()->toTree()->toArray();

        return $this->success('获取菜单列表成功', $menus);
    }

    /**
     * @title 新增菜单
     *
     * @param MenuRequest $request
     *
     * @return JsonResponse
     */
    public function store(MenuRequest $request): JsonResponse
    {
        if ($parentId = $request['parent_id']) {
            $parent = Menu::find($parentId);
            $menu = Menu::create($request->all(), $parent);
        } else {
            $menu = Menu::create($request->all());
        }

        return $this->success('新增成功', $menu);
    }

    /**
     * @title 更新菜单
     *
     * @param MenuRequest $request
     * @param Menu $menu
     *
     * @return JsonResponse
     */
    public function update(MenuRequest $request, Menu $menu): JsonResponse
    {
        $menu->fill($request->all())->save();

        if ($parentId = $request['parent_id']) {
            $parent = Menu::find($parentId);
            $menu->appendToNode($parent)->save();
        } else {
            $menu->saveAsRoot();
        }

        return $this->success('更新成功', $menu);
    }

    /**
     * @title 获取菜单详情
     *
     * @param Menu $menu
     *
     * @return JsonResponse
     */
    public function show(Menu $menu): JsonResponse
    {
        return $this->success('获取详情成功', $menu);
    }

    /**
     * @title 删除菜单
     *
     * @param Menu $menu
     *
     * @return JsonResponse
     */
    public function destroy(Menu $menu): JsonResponse
    {
        return $menu->delete()
            ? $this->success('删除成功')
            : $this->error('删除失败');
    }
}
