<?php

namespace Dcat\Admin\Http\Controllers;

use Carbon\Carbon;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Forms\IssueToken;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Models\PersonalAccessToken;
use Dcat\Admin\Widgets\Modal;

class PersonalAccessTokenController extends AdminController
{

    protected $title = 'Access Tokens';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        config(['admin.enable_default_breadcrumb' => false]);

        return Grid::make((new PersonalAccessToken)
                ->orderByDesc('id')
                ->where('tokenable_id', Admin::user()->id),
            function (Grid $grid) {

                $grid->column('id');
                $grid->column('name');
                $grid->column('last_used_at');
                $grid->column('created_at')->display(fn ($v) => Carbon::createFromTimeString($v)->format("Y-m-d H:i:s"));

                $newButton = Modal::make('New Access Token')
                    ->centered()
                    ->body(IssueToken::make())
                    ->button('<a class="btn btn-primary float-right"><i class="fa fa-key"></i> New Access Token</a>')
                    ->render();
                
                $grid->tools($newButton);
                $grid->disableBatchActions();
                $grid->disableRefreshButton();
                $grid->disableFilter();
                $grid->disableRowSelector();
                $grid->showBatchDelete();
                $grid->showColumnSelector(false);
                $grid->disableEditButton();
                $grid->disableCreateButton();
                $grid->disableViewButton();
            }
        );
    }

    protected function form()
    {
        $model = (new PersonalAccessToken)->where('tokenable_id', Admin::user()->id);
        return Form::make($model, function (Form $form) {});
    }
}
