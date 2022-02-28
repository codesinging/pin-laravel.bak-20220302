<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as PermissionRole;

class Role extends BaseModel
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
        return $this->belongsTo(PermissionRole::class, 'permission_role_id');
    }

    /**
     * @return \Spatie\Permission\Contracts\Role
     */
    public function permissionRole(): \Spatie\Permission\Contracts\Role
    {
        return PermissionRole::findById($this->attributes['permission_role_id']);
    }

    /**
     * 新增角色
     *
     * @param array $data
     * @param string $guard
     *
     * @return Role
     */
    public function store(array $data, string $guard = ''): Role
    {
        $permissionRole = PermissionRole::create([
            'name' => $data['name'],
            'guard_name' => $guard ?: ($data['guard_name'] ?? ''),
        ]);

        $role = $this->create($this->sanitize($data));
        $role->role()->associate($permissionRole)->save();

        return $role;
    }

    /**
     * 同步角色
     *
     * @param Role $role
     * @param array $data
     * @param string $guard
     *
     * @return Role
     */
    public function sync(Role $role, array $data, string $guard = ''): Role
    {
        $role->fill($this->sanitize($data))->save();
        $role->role()->update([
            'name' => $data['name'],
            'guard_name' => $guard ?: ($data['guard_name'] ?? ''),
        ]);
        $role->refresh();

        return $role;
    }

}
