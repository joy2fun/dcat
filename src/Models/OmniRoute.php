<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;

class OmniRoute extends Model
{
    use HasDateTimeFormatter;

    const enabled = [
        1 => "Y",
        0 => "N",
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->init();
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.omni_route_table'));
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

    public static function boot()
    {
        parent::boot();

        self::updated(function(OmniRoute $model) {
            if ($model->wasChanged('response_json')) {
                $model->calls = json_encode([
                    'middleware' => $model->response_json 
                        ? ['admin.auth:sanctum']
                        : ['web', 'admin']
                ], JSON_UNESCAPED_SLASHES);
                $model->saveQuietly();
            }
        });
    }
}
