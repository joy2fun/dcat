<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OmniColumn
 *
 * @property int $id
 * @property string $column_name
 * @property string $table_name
 * @property string $label
 * @property string $input_type
 * @property string|null $default
 * @property string|null $dict
 * @property int $grid_showed 表格: 1=是 0=否
 * @property int $mode 模式: 0=CU 1=C 2=U
 * @property string|null $rules
 * @property string|null $grid_calls
 * @property string|null $filter_calls
 * @property string|null $grid_column_calls
 * @property string|null $form_calls
 * @property string|null $form_column_calls
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn current()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn query()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereColumnName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereDict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereFilterCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereFormCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereFormColumnCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereGridCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereGridColumnCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereGridShowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniColumn whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OmniColumn extends Model
{
    use HasDateTimeFormatter;

    const grid_showed = [
        1 => "Y",
        0 => "N",
    ];

    const mode = [
        0 => "CU",
        1 => "C",
        2 => "U",
        3 => '-',
    ];

    public function scopeCurrent(Builder $query)
    {
        return $query->where('table_name', app('admin.omni')->getTableName());
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.omni_column_table'));
    }

    public function getDictArrayAttribute()
    {
        return $this->dict && $this->dict !== 'null' ? Json::decode($this->dict) : [];
    }

    public function getGridColumnCallsArrayAttribute()
    {
        return $this->grid_column_calls && $this->grid_column_calls !== 'null' ? Json::decode($this->grid_column_calls) : [];
    }

    public function getFormColumnCallsArrayAttribute()
    {
        return $this->form_column_calls && $this->form_column_calls !== 'null' ? Json::decode($this->form_column_calls) : [];
    }
}
