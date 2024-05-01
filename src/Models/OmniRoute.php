<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OmniRoute
 *
 * @property int $id
 * @property string $uri
 * @property string $table_name
 * @property string|null $calls
 * @property int $enabled 启用: 1=是 0=否
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $calls_array
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute query()
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereCalls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OmniRoute whereUri($value)
 * @mixin \Eloquent
 */
class OmniRoute extends Model
{
	use HasDateTimeFormatter;
    
    const enabled = [
        1 => "Y",
        0 => "N",
    ];

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.omni_route_table', 'omni_routes'));
    }

    public function getModelClassAttribute()
    {
        return $this->model_name ?: ($this->soft_deleted ? OmniSoftDelete::class : Omni::class); 
    }

    public function getGridModelCallsArrayAttribute()
    {
        return $this->grid_model_calls && $this->grid_model_calls !== 'null' ? Json::decode($this->grid_model_calls) : []; 
    }

    public function getDetailModelCallsArrayAttribute()
    {
        return $this->detail_model_calls && $this->detail_model_calls !== 'null' ? Json::decode($this->detail_model_calls) : []; 
    }

    public function getCallsArrayAttribute()
    {
        return $this->calls && $this->calls !== 'null' ? Json::decode($this->calls) : []; 
    }

    public function getGridCallsArrayAttribute()
    {
        return $this->grid_calls && $this->grid_calls !== 'null' ? Json::decode($this->grid_calls) : [];
    }

    public function getFormCallsArrayAttribute()
    {
        return $this->form_calls && $this->form_calls !== 'null' ? Json::decode($this->form_calls) : [];
    }

    public function getFilterCallsArrayAttribute()
    {
        return $this->filter_calls && $this->filter_calls !== 'null' ? Json::decode($this->filter_calls) : [];
    }
}