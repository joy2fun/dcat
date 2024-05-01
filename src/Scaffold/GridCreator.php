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

            if (preg_match_all('~(?<keys>[0-9]+)[=]?(?<values>[^\s=,]+)~isx', $field['comment'], $match) > 1) {
                $guessedOptions = str(request('model_name'))->classBasename() . '::' . $field['name'];
                $rows[] = "            \$grid->column('{$field['name']}')->dropdown($guessedOptions);";
                $addtionalFilters .= "                \$filter->equal('{$field['name']}')->select($guessedOptions);\n";
            } else {
                $rows[] = "            \$grid->column('{$field['name']}');";
            }
        }

        $addtionalFilters = trim($addtionalFilters);

        if ($timestamps) {
            $rows[] = '            $grid->column(\'created_at\');';
            $rows[] = '            $grid->column(\'updated_at\')->sortable();';
        }

        $rows[] = <<<EOF
        
            \$grid->filter(function (Grid\Filter \$filter) {
                \$filter->equal('$primaryKey');
                $addtionalFilters
            });
EOF;

        return implode("\n", $rows);
    }
}
