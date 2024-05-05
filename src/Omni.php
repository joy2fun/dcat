<?php

namespace Dcat\Admin;

use Dcat\Admin\Http\Controllers\OmniController;
use Dcat\Admin\Models\OmniColumn;
use Dcat\Admin\Models\OmniRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Omni
{

    /**
     * @var Collection $routes
     */
    protected $routes;

    /**
     * @var Collection 
     */
    protected $columns;

    protected $currentRoute;

    public function __construct()
    {
        $this->routes = OmniRoute::where('enabled', 1)->get();
    }

    public function resolveRoute($id)
    {
        $this->currentRoute = $this->routes->firstWhere('id', '=', $id);
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    public function getTableName()
    {
        return $this->currentRoute->table_name;
    }

    public function getColumns()
    {
        if (is_null($this->columns)) {
            $this->columns = OmniColumn::current()->get();
        }

        return $this->columns;
    }

    public function getColumn($name)
    {
        return $this->getColumns()->firstWhere('column_name', '=', $name);
    }

    public function boot(): void
    {
        if (request()->is('*omni-route*')) {
            return;
        }

        $this->routes->map(function ($route) {
            Route::group([
                'middleware' => 'admin.omni:' . $route->id
            ], function ($router) use ($route) {
                $router = $router->resource($route->uri, OmniController::class);
                foreach ($route->calls_array as $func => $args) {
                    switch ($func) {
                        case "middleware":
                            $router->$func($args);
                            break;
                        default:
                            $this->call($router, $func, $args);
                    }
                }
            });
        });
    }

    public function call($obj, $method, $args)
    {
        if (strpos($method, '#') === 0) {
            return $obj;
        }

        if (is_scalar($args)) {
            return $obj->$method($args);
        } else {
            return $obj->$method(...$args);
        }
    }

    public function scaffold()
    {
        $input = request()->all();

        $filterCalls = [];

        foreach ($input['fields'] as $field) {
            $dict = $this->parseDict($field['comment']);
            $searchable = ($field['nullable'] ?? '') != 'on';
            $gridColumnCalls = [];
            $formColumnCalls = [];

            if ($dict) {
                $gridColumnCalls['dropdown'] = null;
                $formColumnCalls['options'] = null;
            }

            $row = OmniColumn::where([
                'table_name' => $input['table_name'],
                'column_name' => $field['name'],
            ])->first();

            if (!$row) {
                $row = new OmniColumn;
                $row->table_name = $input['table_name'];
                $row->column_name = $field['name'];
                $row->label = $field['translation'] ?: $field['name'];
                $row->column_name = $field['name'];
                $row->input_type = $dict ? 'radio' : 'text';
                $row->default = $field['default'] ?? '';
                $row->grid_showed =  $searchable ? 1 : 0;
                $row->mode = $input['primary_key'] == $field['name'] ? '-' : 'CU';
                $row->rules = '';
                $row->dict = json_encode($dict, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
                $row->grid_column_calls = json_encode($gridColumnCalls, JSON_UNESCAPED_UNICODE);
                $row->form_column_calls = json_encode($formColumnCalls, JSON_UNESCAPED_UNICODE);
                $row->save();
            }
        }

        if ($input['omni_route_uri']) {
            $route = OmniRoute::where([
                // 'table_name' => $input['table_name'],
                'uri' => $input['omni_route_uri'],
            ])->first();
            if (!$route) {
                $route = new OmniRoute;
                $route->uri = $input['omni_route_uri'];
                $route->table_name =  $input['table_name'];
                $route->soft_deleted = ($input['soft_deletes'] ?? 0) ? 1 : 0;
                $route->model_name = class_exists($input['model_name']) ? $input['model_name'] : '';
                $route->filter_calls = json_encode($filterCalls, JSON_UNESCAPED_UNICODE);
                $route->form_calls = '{}';
                $route->grid_calls = '{}';
                $route->detail_model_calls = '{}';
                $route->grid_model_calls = '{}';
                $route->calls = '{}';
                $route->save();
            }
        }
    }

    private function parseDict(string $str)
    {
        $count = preg_match_all('~(?<keys>[0-9]+)[=]?(?<values>[^\s=,]+)~isx', $str, $match);
        if ($count > 1) {
            return array_combine($match['keys'], $match['values']);
        }
        return [];
    }
}
