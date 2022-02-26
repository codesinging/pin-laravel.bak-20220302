<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuthRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthRuleController extends Controller
{
    public function index(AuthRule $authRule, Request $request): JsonResponse
    {
        $lister = $authRule->lister(function (Builder $builder) use ($request) {
            if ($type = $request->get('type')) {
                $builder->where('type', $type);
            }
        });

        return $this->success('获取授权规则成功', $lister);
    }
}
