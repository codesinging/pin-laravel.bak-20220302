<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAction extends BaseModel
{
    protected $fillable = [
        'permission_id',
        'controller',
        'action',
        'controller_title',
        'action_title',
    ];

    protected $with = [
        'permission'
    ];

    /**
     * 当前动作的关联权限模型
     *
     * @return BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(AdminPermission::class, 'permission_id');
    }

    /**
     * 新增动作并同时新增对应的权限
     *
     * @param array $data
     *
     * @return AdminAction
     */
    public function store(array $data): AdminAction
    {
        $permission = AdminPermission::create([
            'name' => $data['name'],
        ]);

        $action = $this->create($this->sanitize($data));
        $action->permission()->associate($permission)->save();

        return $action;
    }
}
