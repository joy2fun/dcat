<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    /**
     * Set description for following 4 action pages.
     *
     * @var array
     */
    protected $description = [
        //        'index'  => 'Index',
        //        'show'   => 'Show',
        //        'edit'   => 'Edit',
        //        'create' => 'Create',
    ];

    protected $breadcrumbs = null;

    /**
     * Set translation path.
     *
     * @var string
     */
    protected $translation;

    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title ?: admin_trans_label();
    }

    /**
     * Get description for following 4 action pages.
     *
     * @return array
     */
    protected function description()
    {
        return $this->description;
    }

    /**
     * Get translation path.
     *
     * @return string
     */
    protected function translation()
    {
        return $this->translation;
    }

    public function breadcrumbs(array $list)
    {
        return $this->breadcrumbs = $list;
    }

    /**
     * Index interface.
     *
     * @param  Content  $content
     * @return Content
     */
    public function index(Content $content)
    {
        /** @var Grid  */
        $grid = $this->grid();
        if (app('admin.omni')->isApiRequest()) {
            $grid->build();
            $paginator = $grid->model()->paginator();
            if ($paginator instanceof LengthAwarePaginator) {
                return Admin::json([
                    'items' => $paginator->items(),
                    'total' => $paginator->total(),
                    'page' => $paginator->currentPage(),
                    'last' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                ]);
            } elseif ($paginator instanceof Paginator) {
                return Admin::json(['items' => $paginator->items()]);
            }
        } else {
            if ($this->breadcrumbs ?? null) {
                call_user_func_array([$content, 'breadcrumb'], $this->breadcrumbs);
            }
            return $content
                ->translation($this->translation())
                ->title($this->title())
                ->description($this->description()['index'] ?? trans('admin.list'))
                ->body($grid);
        }
    }

    /**
     * Show interface.
     *
     * @param  mixed  $id
     * @param  Content  $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        if (app('admin.omni')->isApiRequest()) {
            return Admin::json($this->detail($id)->model()->toArray());
        } else {
            if ($this->breadcrumbs ?? null) {
                call_user_func_array([$content, 'breadcrumb'], $this->breadcrumbs);
            }
            $detail = $this->detail($id);
            return $content
                ->translation($this->translation())
                ->title($this->title())
                ->description($this->description()['show'] ?? trans('admin.show'))
                ->body($detail);
        }
    }

    /**
     * Edit interface.
     *
     * @param  mixed  $id
     * @param  Content  $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        if ($this->breadcrumbs ?? null) {
            call_user_func_array([$content, 'breadcrumb'], $this->breadcrumbs);
        }
        $form = $this->form()->edit($id);
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['edit'] ?? trans('admin.edit'))
            ->body($form);
    }

    /**
     * Create interface.
     *
     * @param  Content  $content
     * @return Content
     */
    public function create(Content $content)
    {
        if ($this->breadcrumbs ?? null) {
            call_user_func_array([$content, 'breadcrumb'], $this->breadcrumbs);
        }
        $body = $this->form();
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['create'] ?? trans('admin.create'))
            ->body($body);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->form()->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->form()->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->form()->destroy($id);
    }
}
