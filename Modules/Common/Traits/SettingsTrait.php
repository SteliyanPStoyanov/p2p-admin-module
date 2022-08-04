<?php

namespace Modules\Common\Traits;

trait SettingsTrait
{
    private static $DEFAULT_ORIGINATOR_FEE_PERCENT = 10;

    /**
     * TODO:
     * read from settings
     *
     * @return [type] [description]
     */
    public function getOriginatorPercent(): int
    {
        return self::$DEFAULT_ORIGINATOR_FEE_PERCENT;
    }
}
