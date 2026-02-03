<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format a date with translated day and month names based on current locale.
     *
     * @param Carbon $date
     * @param string|null $format Format string with 'l' for day name and 'F' for month name. If null, uses locale-aware default.
     * @return string Formatted date with translated names
     */
    public static function formatLocalized(Carbon $date, ?string $format = null): string
    {
        // Use locale-aware default format if not provided
        if ($format === null) {
            $format = app()->getLocale() === 'nl' ? 'l j F Y' : 'l, F j, Y';
        }

        $dayNames = [
            'Sunday' => __('messages.dates.sunday'),
            'Monday' => __('messages.dates.monday'),
            'Tuesday' => __('messages.dates.tuesday'),
            'Wednesday' => __('messages.dates.wednesday'),
            'Thursday' => __('messages.dates.thursday'),
            'Friday' => __('messages.dates.friday'),
            'Saturday' => __('messages.dates.saturday'),
        ];

        $monthNames = [
            'January' => __('messages.dates.january'),
            'February' => __('messages.dates.february'),
            'March' => __('messages.dates.march'),
            'April' => __('messages.dates.april'),
            'May' => __('messages.dates.may'),
            'June' => __('messages.dates.june'),
            'July' => __('messages.dates.july'),
            'August' => __('messages.dates.august'),
            'September' => __('messages.dates.september'),
            'October' => __('messages.dates.october'),
            'November' => __('messages.dates.november'),
            'December' => __('messages.dates.december'),
        ];

        $monthNamesAbbrev = [
            'Jan' => __('messages.dates.january_abbr'),
            'Feb' => __('messages.dates.february_abbr'),
            'Mar' => __('messages.dates.march_abbr'),
            'Apr' => __('messages.dates.april_abbr'),
            'May' => __('messages.dates.may_abbr'),
            'Jun' => __('messages.dates.june_abbr'),
            'Jul' => __('messages.dates.july_abbr'),
            'Aug' => __('messages.dates.august_abbr'),
            'Sep' => __('messages.dates.september_abbr'),
            'Oct' => __('messages.dates.october_abbr'),
            'Nov' => __('messages.dates.november_abbr'),
            'Dec' => __('messages.dates.december_abbr'),
        ];

        // Use preg_replace_callback to safely replace format codes
        $result = preg_replace_callback('/[ldFMjnmYyHis]/', function($matches) use ($date, $dayNames, $monthNames, $monthNamesAbbrev) {
            $code = $matches[0];
            
            switch ($code) {
                case 'l': // Full textual day of the week
                    return $dayNames[$date->format('l')] ?? $date->format('l');
                case 'D': // Three letter day of the week
                    return $date->format('D');
                case 'j': // Day of month without leading zeros
                    return $date->format('j');
                case 'd': // Day of month with leading zeros
                    return $date->format('d');
                case 'F': // Full month name
                    return $monthNames[$date->format('F')] ?? $date->format('F');
                case 'M': // Three letter month name
                    return $monthNamesAbbrev[$date->format('M')] ?? $date->format('M');
                case 'n': // Month number without leading zeros
                    return $date->format('n');
                case 'm': // Month number with leading zeros
                    return $date->format('m');
                case 'Y': // 4-digit year
                    return $date->format('Y');
                case 'y': // 2-digit year
                    return $date->format('y');
                case 'H': // Hour 24-hour
                    return $date->format('H');
                case 'i': // Minutes
                    return $date->format('i');
                case 's': // Seconds
                    return $date->format('s');
                default:
                    return $matches[0];
            }
        }, $format);

        return $result;
    }
}
