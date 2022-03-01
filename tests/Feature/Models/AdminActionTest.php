<?php

namespace Tests\Feature\Models;

use App\Http\Controllers\Admin\AuthController;
use App\Models\AdminAction;
use App\Models\AdminPermission;
use App\Support\Permission\PermissionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionException;
use Tests\TestCase;

class AdminActionTest extends TestCase
{
    use RefreshDatabase;

    protected string $routeAction = AuthController::class . '@login';

    /**
     * @throws ReflectionException
     */
    public function testStore()
    {
        $permissionName = PermissionBuilder::fromRoute($this->routeAction);
        AdminPermission::query()->where('name', $permissionName)->delete();

        $data = PermissionBuilder::actionData($this->routeAction);

        AdminAction::new()->store($data);

        $permission = AdminPermission::findByName($permissionName);
        self::assertNotNull($permission);

        $action = AdminAction::query()->where('permission_id', $permission['id'])->first();

        self::assertNotNull($action);
    }
}
