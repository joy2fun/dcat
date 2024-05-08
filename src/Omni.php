<?php

namespace Dcat\Admin;

use Dcat\Admin\Http\Controllers\OmniColumnController;
use Dcat\Admin\Http\Controllers\OmniController;
use Dcat\Admin\Http\Controllers\OmniRouteController;
use Dcat\Admin\Models\OmniColumn;
use Dcat\Admin\Models\OmniRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Omni
{

    protected $enabled;

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
        $this->enabled = config('admin.omni.enable', false);
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

    public function isApiRequest()
    {
        return $this->enabled 
            && $this->currentRoute?->response_json;
    }

    private function registerRoutes()
    {
        if (config('admin.auth.enable', true)) {
            app('router')->group([
                'prefix'     => config('admin.route.prefix'),
                'middleware' => config('admin.route.middleware'),
            ], function ($router) {
                $router->resource('omni/route', OmniRouteController::class);
                $router->resource('omni/column', OmniColumnController::class);
            });
        }
    }

    public function boot(): void
    {
        $this->registerRoutes();

        if (! $this->enabled) {
            return;
        }

        $this->routes = OmniRoute::where('enabled', 1)->get();

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

    /**
     * force to check if current user is administrator
     */
    public function checkAdministrator()
    {
        $user = Admin::user();
        if (!$user || !$user->isAdministrator()) {
            if (request()->method() != 'GET') {
                abort(403);
            }
        }
    }

    /**
     * filter config example
     * {
     *      "op": "equal",
     *      "column": "name",
     *      "calls": {
     *        "width": 2
     *      }
     *  }
     */
    public function scaffold()
    {
        $input = request()->all();

        $gridModelCalls = [];
        $gridCalls = [];
        $filterCalls = [];

        if ($input['primary_key']) {
            $gridModelCalls['orderByDesc'] = $input['primary_key'];
        }

        $quickSearchColumns = [];

        foreach ($input['fields'] as $field) {
            $dict = $this->parseDict((string) $field['comment']);
            $searchable = ($field['nullable'] ?? '') != 'on';
            $gridColumnCalls = [];
            $formColumnCalls = [];

            if ($searchable) {
                $columnFilter = [
                    'op' => $field['type'] == 'string' ? 'like' : 'equal',
                    'column' => $field['name'],
                ];

                if ($field['key'] ?? null) {
                    $quickSearchColumns[] = $field['name'];
                }
            } else {
                $columnFilter = null;
            }

            if ($dict) {
                $gridColumnCalls['dropdown'] = null;
                $formColumnCalls['options'] = null;
                if ($columnFilter) {
                    $columnFilter['op'] = 'equal';
                    $columnFilter['calls']['select'] = null;
                }
            }

            $row = OmniColumn::where([
                'table_name' => $input['table_name'],
                'column_name' => $field['name'],
            ])->first();

            if (!$row) {
                $row = new OmniColumn;
                $row->table_name = $input['table_name'];
                $row->column_name = $field['name'];
                $row->label = $field['translation'] ?: '';
                $row->column_name = $field['name'];
                $row->input_type = $dict ? 'radio' : 'text';
                $row->default = $field['default'] ?? '';
                $row->grid_showed =  $searchable ? 1 : 0;
                $row->mode = $input['primary_key'] == $field['name'] ? 3 : 0;
                $row->rules = '';
                $row->dict = json_encode($dict, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
                $row->grid_column_calls = json_encode($gridColumnCalls, JSON_UNESCAPED_UNICODE);
                $row->form_column_calls = json_encode($formColumnCalls, JSON_UNESCAPED_UNICODE);
                $row->save();
            }

            if ($columnFilter) {
                $filterCalls[] = $columnFilter;
            }
        }

        if ($quickSearchColumns) {
            $gridCalls['quickSearch'] = $quickSearchColumns;
        }

        if ($input['omni_route_uri']) {
            $route = OmniRoute::where([
                // 'table_name' => $input['table_name'],
                'uri' => $input['omni_route_uri'],
            ])->first();
            if (!$route) {
                $route = new OmniRoute;
                $route->uri = $input['omni_route_uri'];
                $route->conn_name =  $input['conn_name'];
                $route->table_name =  $input['table_name'];
                $route->soft_deleted = ($input['soft_deletes'] ?? 0) ? 1 : 0;
                $route->model_name = class_exists($input['model_name']) ? $input['model_name'] : '';
                $route->filter_calls = json_encode($filterCalls, JSON_UNESCAPED_UNICODE);
                $route->form_calls = '{}';
                $route->grid_calls = json_encode($gridCalls, JSON_UNESCAPED_UNICODE);
                $route->detail_model_calls = '{}';
                $route->grid_model_calls = json_encode($gridModelCalls, JSON_UNESCAPED_UNICODE);
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
