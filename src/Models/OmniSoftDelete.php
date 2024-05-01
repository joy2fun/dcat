<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Omni
 *
 * @method static Builder|Omni newModelQuery()
 * @method static Builder|Omni newQuery()
 * @method static Builder|Omni query()
 * @mixin \Eloquent
 */
class OmniSoftDelete extends Omni
{
    use SoftDeletes;
}
