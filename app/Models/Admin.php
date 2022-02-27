<?php

namespace App\Models;

use App\Support\Model\AuthModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Traits\HasRoles;

class Admin extends AuthModel
{
    use HasRoles;

    protected string $guard_name = 'sanctum';

    const GUARD = 'sanctum';

    protected $fillable = [
        'username',
        'name',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'boolean',
        'super' => 'boolean',
    ];

    /**
     * 密码自动加密
     *
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return new Attribute(set: fn($value) => bcrypt($value));
    }

    /**
     * 是否超级管理员
     *
     * @return bool
     */
    public function isSuper(): bool
    {
        return (boolean)$this->attributes['super'];
    }

    /**
     * 获取角色
     * @return Collection|array
     */
    public function getRoles(): Collection|array
    {
        $permissionRoleIds = $this->roles()->get()->pluck('id');
        return Role::query()->whereIn('permission_role_id', $permissionRoleIds)->get();
    }
}
