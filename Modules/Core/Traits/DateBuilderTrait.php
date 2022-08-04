<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Carbon;

trait DateBuilderTrait
{
    public static string $dateRangeRegex = '/^([0-9]{2}\.[0-9]{2}\.[0-9]{4} - [0-9]{2}\.[0-9]{2}\.[0-9]{4})$/i';
    public static string $formatDateRangeBegin = 'Y-m-d 00:00:00';
    public static string $formatDateRangeEnd = 'Y-m-d 23:59:59';

    /**
     * Returns array with from/to date ranges, transformed to: yyyy-mm-dd h:m:s
     *
     * @param string $dateRange - accepted formt: dd-mm-yyy - dd-mm-yyyy
     *
     * @return array
     */
    public function extractDates(string $dateRange): array
    {
        $dates = explode(' - ', $dateRange);

        return [
            'from' => $this->fmt($dates[0], self::$formatDateRangeBegin),
            'to' => $this->fmt($dates[1], self::$formatDateRangeEnd),
        ];
    }

    /**
     * Short alias to Carbon parse and format
     *
     * @param string $date
     * @param string $fmt
     *
     * @return string
     */
    public function fmt(string $date, string $fmt): string
    {
        return Carbon::parse($date)->format($fmt);
    }
}
