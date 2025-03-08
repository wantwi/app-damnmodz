<?php
function timeago($time, $timezone = 'UTC', $tense = 'ago')
{
    static $periods = array('year', 'month', 'day', 'hour', 'minute', 'second');
    if (!(strtotime($time) > 0)) {
        return trigger_error("Wrong time format: '$time'", E_USER_ERROR);
    }

    // Convert $time to DateTime object with user's timezone
    $dateTime = new DateTime($time, new DateTimeZone('UTC')); // Assuming stored as UTC
    $dateTime->setTimezone(new DateTimeZone($timezone)); // Convert to user's timezone

    $now  = new DateTime('now', new DateTimeZone($timezone)); // Current time in user's timezone
    $diff = $now->diff($dateTime)->format('%y %m %d %h %i %s');
    $diff = explode(' ', $diff);
    $diff = array_combine($periods, $diff);
    $diff = array_filter($diff);
    $period = key($diff);
    $value  = current($diff);

    if (!$value) {
        $period = '';
        $tense = '';
        $value  = 'just now';
    } else {
        if ($period == 'day' && $value >= 7) {
            $period = 'week';
            $value  = floor($value / 7);
        }
        if ($value > 1) {
            $period .= 's';
        }
    }
    return "$value $period $tense";
}

