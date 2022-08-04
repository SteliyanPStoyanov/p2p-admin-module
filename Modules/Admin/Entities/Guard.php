<?php

namespace Modules\Admin\Entities;

use Spatie\Permission\Guard as SpatieGuard;

class Guard extends SpatieGuard
{
    public const DEFAULT_GUARD_NAME = 'web';
}
