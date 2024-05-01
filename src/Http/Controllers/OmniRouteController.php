<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Models\OmniRoute;
use Illuminate\Database\Eloquent\Casts\Json;

class OmniRouteController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make((new OmniRoute)->orderByDesc('id'), function (Grid $grid) {
            $grid->column('id')->link(fn ($v) => admin_url('omni-route/'.$v.'/edit'));
            $grid->column('uri')->copyable();
            $grid->column('enabled')->dropdown(OmniRoute::enabled);
            $grid->column('soft_deleted')->dropdown(OmniRoute::enabled);
            $grid->column('table_name')->link(fn ($v) => admin_url('omni-column?table_name=' . $v));
            $grid->column('model_name')->link(fn ($v) => admin_url('?show_source=' . $v), '_blank');
            $grid->column('calls')->display(fn ($v) => sprintf('<pre>%s</pre>', ($v)));
            $grid->column('enabled')->dropdown(OmniRoute::enabled);
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('table_name')->width(2);
                $filter->like('uri')->width(2);
                $filter->equal('enabled')->select(OmniRoute::enabled)->width(2);
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new OmniRoute(), function (Show $show) {
            $show->field('id');
            $show->field('uri');
            $show->field('table_name');
            $show->field('calls');
            $show->field('enabled');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new OmniRoute(), function (Form $form) {
            $form->radio('enabled')->options(OmniRoute::enabled)->default("1");
            $form->radio('soft_deleted')->options(OmniRoute::enabled)->default("1");
            $form->text('uri')->required();
            $form->text('table_name')->required();
            $form->text('model_name');
            $form->jsoneditor('calls')->rules('json');
            $form->jsoneditor('grid_model_calls')->rules('json');
            $form->jsoneditor('filter_calls')->rules('json');
            $form->jsoneditor('detail_model_calls')->rules('json');
            $form->jsoneditor('grid_calls')->rules('json');
            $form->jsoneditor('form_calls')->rules('json');
        });
    }
}
