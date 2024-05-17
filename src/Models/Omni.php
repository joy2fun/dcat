<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\Appendable;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Omni
 *
 * @method static Builder|Omni newModelQuery()
 * @method static Builder|Omni newQuery()
 * @method static Builder|Omni query()
 * @mixin \Eloquent
 */
class Omni extends Model
{
    use HasDateTimeFormatter;
    use Appendable;

    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $route = app('admin.omni')->getCurrentRoute();
        if (! $route) {
            abort(404);
        }

        $connection = $route->conn_name 
            ?: config('admin.database.connection') 
            ?: config('database.default');

        $this->setConnection($connection);
        $this->setTable(app('admin.omni')->getTableName());
        $this->timestamps = $route->getOriginal('timestamps') > 0;
    }
}
