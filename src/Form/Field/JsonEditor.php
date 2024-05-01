<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class JsonEditor extends Field
{
    protected function formatFieldData($data)
    {
        // 获取到当前字段值
        $value = parent::formatFieldData($data);

        if (!is_scalar($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return $value;
    }

}