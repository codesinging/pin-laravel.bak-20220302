<?php

namespace Database\Seeders;

use App\Models\AdminRule;
use App\Support\Reflection\ClassReflection;
use App\Support\Routing\RouteParser;
use Illuminate\Database\Seeder;
use Illuminate\Routing\Route;
use ReflectionException;

class AdminRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run()
    {
        $this->parseRules('api/admin');
    }

    /**
     * @throws ReflectionException
     */
    protected function parseRules(string $prefix)
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
            $type = 'route';

            $name = $parser->permissionRule($type);

            $data = [
                'type' => $type,
                'module' => $parser->module(),
                'controller' => $parser->controller(),
                'action' => $parser->action(),
                'controller_title' => $controllerTitle,
                'action_title' => $actionTitle,
            ];

            $this->syncDatabase($name, $data);
        }
    }

    protected function syncDatabase(string $name, array $data)
    {
        (new AdminRule())->updateOrCreate(['name' => $name], $data);
    }
}
