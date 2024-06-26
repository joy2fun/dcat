<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Models\OmniRoute;

class OmniRouteController extends AdminController
{

    protected $title = 'Routes';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make((new OmniRoute)->orderByDesc('id'), function (Grid $grid) {
            $grid->column('id')->link(fn ($v) => admin_url('omni/route/' . $v . '/edit'));
            $grid->column('title')->editable();
            $grid->column('uri')->link(fn ($v) => '/' . $v, '_blank');
            $grid->column('enabled')->dropdown(OmniRoute::enabled);
            $grid->column('response_json', 'JSON API')->dropdown(OmniRoute::enabled);
            $grid->column('timestamps')->dropdown(OmniRoute::enabled)->hide();
            $grid->column('soft_deleted')->dropdown(OmniRoute::enabled)->hide();
            $grid->column('conn_name')->hide();
            $grid->column('table_name')->link(fn ($v) => admin_url('omni/column?table_name=' . $v));
            $grid->column('model_name')->link(fn ($v) => admin_url('?show_source=' . $v), '_blank');
            $grid->column('calls')->display(fn ($v) => sprintf('<pre>%s</pre>', ($v)))->hide();
            $grid->column('enabled')->dropdown(OmniRoute::enabled);

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel()->expand();
                $filter->equal('conn_name', 'Conn')->width(2);
                $filter->equal('table_name', 'Table')->width(2);
                $filter->like('uri')->width(2);
                $filter->equal('enabled')->select(OmniRoute::enabled)->width(2);
                $filter->equal('response_json', 'JSON API')->select(OmniRoute::enabled)->width(2);
            });

            $grid->showColumnSelector();
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
        app('admin.omni')->checkAdministrator();
        return Form::make(new OmniRoute(), function (Form $form) {
            $form->text('title');
            $form->radio('enabled')->options(OmniRoute::enabled)->default("1");
            $form->radio('soft_deleted')->options(OmniRoute::enabled)->default("1");
            $form->radio('response_json')->options(OmniRoute::enabled)->default(0);
            $form->radio('timestamps')->options(OmniRoute::enabled)->default(0);
            $form->text('uri')->required();
            $form->text('conn_name');
            $form->text('table_name')->required();
            $form->text('model_name');
            $form->jsoneditor('calls')->rules('json')
->help(
'<pre>
{
  "middleware": ["web", "admin"]
}
</pre>');
            $form->jsoneditor('grid_model_calls')->rules('json')
                ->help($this->helpLink('Query Builder', 'https://laravel.com/api/11.x/Illuminate/Database/Query/Builder.html'));
            $form->jsoneditor('filter_calls')->rules('json')
                ->help($this->helpClassLink('help', 'Dcat\Admin\Grid\Filter'));
            $form->jsoneditor('detail_model_calls')->rules('json');
            $form->jsoneditor('grid_calls')->rules('json')
->help($this->helpClassLink('methods', 'Dcat\Admin\Grid', 1) . 
'<pre>
{
  "showColumnSelector": [],
  "export": [
    "ExporterClass or null",
    [
      "filename": "test",
    ]
  ],
  "exportAppends": [],
  "withAppends: []
}
</pre>');
            $form->jsoneditor('form_calls')->rules('json')
                ->help($this->helpClassLink('help', 'Dcat\Admin\Widgets\Form'));
        });
    }

    private function helpClassLink($text, $class, $methods=0)
    {
        return sprintf('<a href="%s" target="_blank">%s</a>', admin_url("?show_source=$class&methods=$methods"), $text);
    }

    private function helpLink($text, $link)
    {
        return sprintf('<a href="%s" target="_blank">%s</a>', $link, $text);
    }
}
