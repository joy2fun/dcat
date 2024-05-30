<?php

namespace Dcat\Admin\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class IssueToken extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $token = request()->user()->createToken($input['name']);

        return $this
            ->response()
            ->success('仅展示一次，前请妥善保存')
            ->alert()
            ->detail($token->plainTextToken)
            ->script('$(".modal.show").modal("hide")');
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('name')->prepend()->required()->width(7, 3);
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [];
    }
}
