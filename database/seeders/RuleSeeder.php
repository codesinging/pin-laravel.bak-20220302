<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Rule;
use App\Support\Routing\RouteParser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Routing\Route;
use ReflectionException;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run()
    {
        $this->parseRules('api/admin', Admin::GUARD);
    }

    /**
     * @throws ReflectionException
     */
    protected function parseRules(string $prefix, string $guard)
    {
        $routes = RouteParser::routes($prefix);

        foreach ($routes as $route) {
            $this->parseRoute($route, $guard);
        }
    }

    /**
     * @throws ReflectionException
     */
    protected function parseRoute(Route $route, string $guard)
    {
        $parser = new RouteParser($route);

        if (!is_null($parser->controllerTitle()) && !is_null($parser->actionTitle())){
            $type = 'route';

            $name = sprintf('%s:%s/%s@%s', $type, $parser->module(), $parser->controller(), $parser->action());

            $data = [
                'type' => $type,
                'guard' => $guard,
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
        (new Rule())->updateOrCreate(['name' => $name], $data);
    }
}
