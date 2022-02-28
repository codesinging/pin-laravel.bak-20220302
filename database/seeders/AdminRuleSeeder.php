<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminRule;
use App\Support\Routing\RouteParser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        if (!is_null($parser->controllerTitle()) && !is_null($parser->actionTitle())){
            $type = 'route';

            $name = sprintf('%s:%s/%s@%s', $type, $parser->module(), $parser->controller(), $parser->action());

            $data = [
                'type' => $type,
                'module' => $parser->module(),
                'controller' => $parser->controller(),
                'action' => $parser->action(),
                'controller_title' => $parser->controllerTitle(),
                'action_title' => $parser->actionTitle(),
            ];

            $this->syncDatabase($name, $data);
        }
    }

    protected function syncDatabase(string $name, array $data)
    {
        (new AdminRule())->updateOrCreate(['name' => $name], $data);
    }
}
