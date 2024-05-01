<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Dropdown extends AbstractDisplayer
{
    public function display($options = [], $refresh = false)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        return Admin::view('admin::grid.displayer.dropdown', [
            'column'  => $this->column->getName(),
            'value'   => $this->value,
            'url'     => $this->url(),
            'options' => $options,
            'refresh' => $refresh,
        ]);
    }

    protected function url()
    {
        return $this->resource().'/'.$this->getKey();
    }
}
