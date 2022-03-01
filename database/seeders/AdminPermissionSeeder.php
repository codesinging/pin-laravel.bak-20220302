<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Support\Permission\PermissionName;
use App\Support\Reflection\ClassReflection;
use App\Support\Routing\RouteParser;
use Illuminate\Database\Seeder;
use Illuminate\Routing\Route;
use ReflectionException;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run()
    {
        $this->parsePermissions('api/admin');
    }

    /**
     * @throws ReflectionException
     */
    protected function parsePermissions(string $prefix)
    {
        $routes = RouteParser::routes($prefix);

        foreach ($routes as $route) {
            $this->parseRoute($route);
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function parseRoute(Route $route)
    {
        $parser = new RouteParser($route);

        $reflection = new ClassReflection($parser->class());

        $controllerTitle = $reflection->classTitle();
        $actionTitle = $reflection->methodTitle($parser->action());

        if (!is_null($controllerTitle) && !is_null($actionTitle)){

            $name = PermissionName::fromRouteParser($parser);

            $data = [
                'module' => $parser->module(),
                'controller' => $parser->controller(),
                'action' => $parser->action(),
                'controller_title' => $controllerTitle,
                'action_title' => $actionTitle,
            ];

            $this->syncPermissions($name, $data);
        }
    }

    protected function syncPermissions(string $name, array $data)
    {
        (new AdminPermission())->updateOrCreate(['name' => $name], $data);
    }
}
