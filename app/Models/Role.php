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

    public function role(): BelongsTo
    {
        return $this->belongsTo(PermissionRole::class, 'permission_role_id');
    }

}
