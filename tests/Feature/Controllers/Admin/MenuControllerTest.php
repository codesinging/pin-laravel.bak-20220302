<?php

namespace Tests\Feature\Controllers\Admin;

use App\Exceptions\ErrorCode;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\ActingAsAdmin;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdmin;

    public function testIndex()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/menus')
            ->assertJsonPath('code', ErrorCode::OK)
            ->assertJsonStructure(['code', 'message', 'data'])
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonPath('data.1.children.2.children.0.path', 'roles')
            ->assertOk();
    }

    public function testStore()
    {
        Menu::truncate();

        $this->actingAsAdmin()
            ->postJson('api/admin/menus', [
                'name' => 'menu_1'
            ])
            ->assertJsonPath('data.id', 1)
            ->assertJsonPath('data.name', 'menu_1')
            ->assertJsonPath('data.parent_id', null)
            ->assertOk();

        $menu_1 = Menu::first();

        $this->actingAsAdmin()
            ->postJson('api/admin/menus', [
                'name' => 'menu_2',
                'parent_id' => $menu_1['id'],
            ])
            ->assertJsonPath('data.id', 2)
            ->assertJsonPath('data.name', 'menu_2')
            ->assertJsonPath('data.parent_id', $menu_1['id'])
            ->assertOk();

        $tree = Menu::all()->toTree()->toArray();
        self::assertArrayHasKey('children', $tree[0]);
        self::assertEquals(2, Arr::get($tree, '0.children.0.id'));
        self::assertEquals(1, Arr::get($tree, '0.children.0.parent_id'));
    }

    public function testUpdate()
    {
        $this->actingAsAdmin()
            ->getJson('api/admin/menus')
            ->assertJsonStructure(['code', 'message', 'data'])
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonPath('data.1.children.2.children.0.path', 'roles')
            ->assertOk()
            ->assertJsonPath('code', ErrorCode::OK);

        $nodeMenus = Menu::where('path', 'menus')->first();

        // 测试修改菜单信息，并移动到根节点
        $this->actingAsAdmin()
            ->putJson('api/admin/menus/' . $nodeMenus['id'], [
                'name' => 'Menus'
            ])
            ->assertJsonPath('data.name', 'Menus')
            ->assertJsonPath('data.parent_id', null)
            ->assertJsonPath('code', 0)
            ->assertOk();

        $nodeAuthorizations = Menu::where('path', 'authorizations')->first();

        // 测试修改菜单并移动到根节点

        $this->actingAsAdmin()
            ->putJson('api/admin/menus/' . $nodeAuthorizations['id'], [
                'name' => 'Authorization',
                'parent_id' => $nodeMenus['id'],
            ])
            ->assertJsonPath('data.name', 'Authorization')
            ->assertJsonPath('data.parent_id', $nodeMenus['id'])
            ->assertOk();

        // 测试修改后的节点关系
        $this->actingAsAdmin()
            ->getJson('api/admin/menus')
            ->assertJsonPath('data.2.path', 'menus')
            ->assertJsonPath('data.2.children.0.path', 'authorizations')
            ->assertJsonPath('data.2.children.0.children.0.path', 'roles')
            ->assertOk();
    }

    public function testDestroy()
    {
        $this->assertDatabaseHas('menus', ['path' => 'roles']);

        $node = Menu::where('path', 'authorizations')->first();

        $this->actingAsAdmin()
            ->deleteJson('api/admin/menus/'.$node['id'])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->assertDatabaseMissing('menus', ['path' => 'authorizations']);
        $this->assertDatabaseMissing('menus', ['path' => 'roles']);
    }
}
