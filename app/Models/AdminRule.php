<?php

namespace App\Models;

use App\Support\Model\BaseModel;

class AdminRule extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
        'guard',
        'module',
        'controller',
        'action',
        'controller_title',
        'action_title',
    ];
}
