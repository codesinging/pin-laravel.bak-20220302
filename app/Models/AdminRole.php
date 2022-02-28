<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Contracts\Role;

class AdminRole extends BaseModel
{
    protected $fillable = [
        'permission_role_id',
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
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminPermissionRole::class, 'permission_role_id');
    }

    /**
     * @return Role
     */
    public function permissionRole(): Role
    {
        return AdminPermissionRole::findById($this->attributes['permission_role_id']);
    }

    /**
     * 新增角色
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
     * 同步角色
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
