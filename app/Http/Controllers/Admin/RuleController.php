<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    /**
     * @title 获取授权规则
     *
     * @param AdminPermission $authRule
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(AdminPermission $authRule, Request $request): JsonResponse
    {
        $lister = $authRule->lister(function (Builder $builder) use ($request) {
            if ($type = $request->get('type')) {
                $builder->where('type', $type);
            }
        });

        return $this->success('获取授权规则成功', $lister);
    }
}
