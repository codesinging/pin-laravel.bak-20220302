<?php

namespace App\Models;

use App\Support\Model\BaseModel;

class AuthRule extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
        'module',
        'controller',
        'action',
        'controller_title',
        'action_title',
    ];
}
