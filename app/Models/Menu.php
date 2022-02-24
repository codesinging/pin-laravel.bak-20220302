<?php

namespace App\Models;

use App\Support\Model\BaseModel;
use Kalnoy\Nestedset\NodeTrait;

class Menu extends BaseModel
{
    use NodeTrait;

    protected $fillable = [
        'name',
        'url',
        'path',
        'icon',
        'sort',
        'is_home',
        'is_opened',
        'status',
    ];

    protected $casts = [
        'is_home' => 'boolean',
        'is_opened' => 'boolean',
        'status' => 'boolean',
    ];
}
