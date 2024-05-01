<?php

namespace Dcat\Admin\Scaffold;

trait FormCreator
{
    /**
     * @param  string  $primaryKey
     * @param  array  $fields
     * @param  bool  $timestamps
     * @return string
     */
    protected function generateForm(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [''];

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }

            $rows[] = $this->buildRow($field);
        }
        if ($timestamps) {
            $rows[] = <<<'EOF'
        
            $form->display('created_at');
            $form->display('updated_at');
EOF;
        }

        return implode("\n", $rows);
    }

    protected function buildRow($field)
    {
        $row = '            ';

        $count = preg_match_all('~(?<keys>[0-9]+)[=]?(?<values>[^\s=,]+)~isx', $field['comment'], $match);

        if ($count > 1) {
            $field['type'] = 'enum';
        }

        switch($field['type']) {
            case 'timestamp':
            case 'dateTime':
                $row .= "\$form->datetime('{$field['name']}')";
                break;

            case 'integer':
            case 'unsignedInteger':
            case 'tinyInteger':
            case 'unsignedTinyInteger':
            case 'smallInteger':
            case 'unsignedSmallInteger':
            case 'mediumInteger':
            case 'unsignedMediumInteger':
            case 'bigInteger':
            case 'unsignedBigInteger':
                $row .= "\$form->number('{$field['name']}')";
                break;

            case 'date':
                $row .= "\$form->date('{$field['name']}')";
                break;

            case 'enum':
                $guessedOptions = str(request('model_name'))->classBasename() . '::' . $field['name'];
                $row .= "\$form->radio('{$field['name']}')->options($guessedOptions)";
                break;

            case 'json':
                $row .= "\$form->jsoneditor('{$field['name']}')";
                break;

            default:
                $row .= "\$form->text('{$field['name']}')";
        }

        if (strlen($field['default']) && "CURRENT_TIMESTAMP" != $field['default']) {
            $row .= sprintf('->default("%s")', $field['default']);
        }

        $row .= ';';
        return $row;
    }
}
