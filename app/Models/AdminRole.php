<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Contracts\Role;

class AdminRole extends BaseModel
{
    protected $fillable = [
        'role_id',
        'description',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $with = [
        'role'
    ];

    /**
     * 当前管理员角色的关联权限角色模型
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminPermissionRole::class, 'role_id');
    }

    /**
     * 当前管理员角色对应的权限角色模型
     * @return Role
     */
    public function relatedRole(): Role
    {
        return AdminPermissionRole::findById($this->attributes['role_id']);
    }

    /**
     * 新增管理员角色并同步新增权限角色
     *
     * @param array $data
     *
     * @return AdminRole
     */
    public function store(array $data): AdminRole
    {
        $permissionRole = AdminPermissionRole::create([
            'name' => $data['name'],
        ]);

        $role = $this->create($this->sanitize($data));
        $role->role()->associate($permissionRole)->save();

        return $role;
    }

    /**
     * 更新管理员角色并同步更新对应的权限角色
     *
     * @param AdminRole $role
     * @param array $data
     *
     * @return AdminRole
     */
    public function sync(AdminRole $role, array $data): AdminRole
    {
        $role->fill($this->sanitize($data))->save();
        $role->role()->update([
            'name' => $data['name'],
        ]);
        $role->refresh();

        return $role;
    }

}
