<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Models\OmniColumn;

class OmniColumnController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make((new OmniColumn)->orderByDesc('id'), function (Grid $grid) {
            $grid->column('table_name', 'table')->link(fn ($v) => admin_url('omni/route?table_name='.$v));
            $grid->column('column_name', 'column')->editable();
            $grid->column('label')->editable();
            $grid->column('input_type')->editable();
            $grid->column('default')->editable();
            $grid->column('grid_showed', 'In grid')->dropdown(OmniColumn::grid_showed);
            $grid->column('mode')->dropdown(OmniColumn::mode);
            $grid->column('rules')->editable();
            $grid->column('form_column_calls')->hide();
            $grid->column('grid_column_calls')->hide();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel()->expand();
                $filter->equal('table_name', 'table')->width(2);
                $filter->equal('column_name', 'column')->width(2);
                $filter->equal('grid_showed', 'in grid')->select(OmniColumn::grid_showed)->width(2);
                $filter->equal('mode')->select(OmniColumn::mode)->width(2);
            });

            $grid->showBatchDelete();
            $grid->showRowSelector();
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
        return Show::make($id, new OmniColumn(), function (Show $show) {
            $show->field('id');
            $show->field('table_name');
            $show->field('column_name');
            $show->field('label');
            $show->field('input_type');
            $show->field('default');
            $show->field('dict');
            $show->field('grid_showed');
            $show->field('mode');
            $show->field('rules');
            $show->field('grid_calls');
            $show->field('filter_calls');
            $show->field('grid_column_calls');
            $show->field('form_calls');
            $show->field('form_column_calls');
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
        return Form::make((new OmniColumn)->orderByDesc('id'), function (Form $form) {
            
            $form->text('table_name');
            $form->text('column_name');
            $form->text('label');
            $form->text('input_type');
            $form->text('default');
            $form->radio('grid_showed')->options(OmniColumn::grid_showed)->default("1");
            $form->radio('mode')->options(OmniColumn::mode)->default(0);
            $form->text('rules')->help($this->helpLink('rules', 'https://laravel.com/docs/11.x/validation#available-validation-rules'));
            $form->jsoneditor('dict')->rules('json');
            $form->jsoneditor('grid_column_calls')->rules('json')
                ->help($this->helpClassLink('help', 'Dcat\Admin\Grid\Column'));
            $form->jsoneditor('form_column_calls')->rules('json')
                ->help($this->helpClassLink('help', 'Dcat\Admin\Form\Field'));
        });
    }

    private function helpClassLink($text, $class)
    {
        return sprintf('<a href="%s" target="_blank">%s</a>', admin_url('?show_source=' . $class), $text);
    }

    private function helpLink($text, $link)
    {
        return sprintf('<a href="%s" target="_blank">%s</a>', $link, $text);
    }
}
