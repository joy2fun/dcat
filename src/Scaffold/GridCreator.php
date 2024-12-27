<?php

namespace Dcat\Admin\Scaffold;

trait GridCreator
{
    /**
     * @param  string  $primaryKey
     * @param  array  $fields
     * @return string
     */
    protected function generateGrid(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [
            "\$grid->column('{$primaryKey}')->sortable();",
        ];

        $addtionalFilters = '';

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }

            $rows[] = "            \$grid->column('{$field['name']}');";
        }

        $addtionalFilters = trim($addtionalFilters);

        if ($timestamps) {
            $rows[] = '            $grid->column(\'created_at\');';
            $rows[] = '            $grid->column(\'updated_at\')->sortable();';
        }

        $classBasename = str(request('model_name'))->classBasename();

        $rows[] = <<<EOF

            \$grid->filter(function (Grid\Filter \$filter) {
                /** @var Grid\Filter<$classBasename> \$filter */
                \$filter->equal('$primaryKey');
                $addtionalFilters
            });
EOF;

        return implode("\n", $rows);
    }
}
