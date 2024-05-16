<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Models\OmniColumn;

class OmniController extends AdminController
{

    protected function grid()
    {
        $omni = app('admin.omni');
        $currentRoute = $omni->getCurrentRoute();
        $this->setTitle($currentRoute?->title);
        $model = new $currentRoute->modelClass;

        foreach($currentRoute->gridModelCallsArray as $func => $args) {
            $model = $omni->call($model, $func, $args);
        }

        return Grid::make($model, function (Grid $grid) use ($omni) {
            foreach($omni->getCurrentRoute()->gridCallsArray as $func => $args) {
                if ($func == 'export') {
                    list($exporterClass, $exporterCalls) = $args;
                    $exporter = $grid->export($exporterClass);
                    if ($exporterCalls) {
                        foreach($exporterCalls as $exportFunc => $exportArgs) {
                            $omni->call($exporter, $exportFunc, $exportArgs);
                        }
                    }
                } else {
                    $omni->call($grid, $func, $args);
                }
            }

            $grid->filter(function (Grid\Filter $filter) use ($omni) {
                foreach($omni->getCurrentRoute()->filterCallsArray as $settings) {
                    $column = $omni->getColumn($settings['column']);
                    $op = $settings['op'];
                    $_ = $filter->$op($settings['column'], $settings['label'] ?? $column->label);
                    foreach($settings['calls'] ?? [] as $func => $args) {
                        if (in_array($func, ['select'])) {
                            $_->$func($column->dictArray);
                        } else {
                            $omni->call($_, $func, $args);
                        }
                    }
                }
            });

            if ($omni->isApiRequest()) {
                return ;
            }

            foreach($omni->getColumns() as $options) {
                /** @var OmniColumn $options */
                if (! $options->grid_showed) continue;

                $_ = $grid->column($options->column_name, $options->label);
                foreach($options->gridColumnCallsArray as $func => $args) {
                    if (in_array($func, ['dropdown', 'select', 'using'])) {
                        $_->$func($options->dictArray);
                    } else {
                        $omni->call($_, $func, $args);
                    }
                }
            }
        });
    }

    protected function form()
    {
        $omni = app('admin.omni');
        $currentRoute = $omni->getCurrentRoute();
        $this->setTitle($currentRoute?->title);
        $model = new $currentRoute->modelClass;

        foreach($currentRoute->detailModelCallsArray as $func => $args) {
            $model = $omni->call($model, $func, $args);
        }

        return Form::make($model, function (Form $form) use ($omni) {
            
            foreach($omni->getCurrentRoute()->formCallsArray as $func => $args) {
                $omni->call($form, $func, $args);
            }

            foreach($omni->getColumns() as $options) {
                if ($options->mode == 3) {
                    continue;
                }

                $_ = $form->{$options['input_type'] ?? 'text'}($options->column_name, $options->label ?: null);

                if ($options->rules) {
                    $_->rules($options->rules);
                }

                // skip for api requests
                if ($omni->isApiRequest()) {
                    continue;
                }

                foreach($options->formColumnCallsArray as $func => $args) {
                    if (in_array($func, ['options'])) {
                        $_->$func($options->dictArray);
                    } elseif (is_scalar($args)) {
                        if ($func == 'options') {
                            $_->options($args()->pluck('name', 'id'));
                        } else {
                            $_->$func($args);
                        }
                    } else {
                        $_->$func(...$args);
                    }
                }
            }
        });
    }

    protected function detail($id)
    {
        $omni = app('admin.omni');
        $currentRoute = $omni->getCurrentRoute();
        $model = new $currentRoute->modelClass;

        foreach($currentRoute->detailModelCallsArray as $func => $args) {
            $model = $omni->call($model, $func, $args);
        }

        return Show::make($id, $model, function (Show $show) {
            $show->field('id');
        });
    }
}
