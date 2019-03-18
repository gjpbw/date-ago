<?php

namespace Gjpbw\DateAgo;

class DateAgo
{
//**************************************************************************************************************************************************
// Adaptation for Laravel
// Original: https://github.com/bezumkin/DateAgo
//**************************************************************************************************************************************************

    /**
     * Formats date to "10 minutes ago" or "Yesterday in 22:10"
     * This algorithm taken from https://github.com/livestreet/livestreet/blob/7a6039b21c326acf03c956772325e1398801c5fe/engine/modules/viewer/plugs/function.date_format.php
     *
     * @param string $date Timestamp to format
     * @param string $dateFormat
     *
     * @return string
     */
    public function dateFormat($date, $dateFormat = null)
    {
        $output = '';
        if (!empty($date)) {
            $date = preg_match('/^\d+$/', $date)
                ? $date
                : strtotime($date);
            $dateFormat = !empty($dateFormat)
                ? $dateFormat
                : config('gjpbw.date-ago.dateFormat');
            $current = time();
            $delta = $current - $date;

            if (config('gjpbw.date-ago.dateNow')) {
                if ($delta < config('gjpbw.date-ago.dateNow')) {
                    return trans('gjpbw.date-ago::date.now');
                }
            }

            if (config('gjpbw.date-ago.dateMinutes')) {
                $minutes = round(($delta) / 60);
                if ($minutes < config('gjpbw.date-ago.dateMinutes')) {
                    if ($minutes > 0) {
                        return $this->declension($minutes,
                            trans('gjpbw.date-ago::date.minutes_back', array('minutes' => $minutes)));
                    } else {
                        return trans('gjpbw.date-ago::date.minutes_back_less');
                    }
                }
            }

            if (config('gjpbw.date-ago.dateHours')) {
                $hours = round(($delta) / 3600);
                if ($hours < config('gjpbw.date-ago.dateHours')) {
                    if ($hours > 0) {
                        return $this->declension($hours,
                            trans('gjpbw.date-ago::date.hours_back', array('hours' => $hours)));
                    } else {
                        return trans('gjpbw.date-ago::date.hours_back_less');
                    }
                }
            }

            if (config('gjpbw.date-ago.dateDay')) {
                switch (date('Y-m-d', $date)) {
                    case date('Y-m-d'):
                        $day = trans('gjpbw.date-ago::date.today');
                        break;
                    case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))):
                        $day = trans('gjpbw.date-ago::date.yesterday');
                        break;
                    case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))):
                        $day = trans('gjpbw.date-ago::date.tomorrow');
                        break;
                    default:
                        $day = null;
                }
                if ($day) {
                    $format = str_replace("day", preg_replace("#(\w{1})#", '\\\${1}', $day), config('gjpbw.date-ago.dateDay'));

                    return date($format, $date);
                }
            }

            $m = date("n", $date);
            $month_arr = json_decode(trans('gjpbw.date-ago::date.months'), true);
            $month = $month_arr[$m - 1];

            $format = preg_replace("~(?<!\\\\)F~U", preg_replace('~(\w{1})~u', '\\\${1}', $month), $dateFormat);

            $output = date($format, $date);
        }
        return $output;
    }
//**************************************************************************************************************************************************
    /**
     * Declension of words
     * This algorithm taken from https://github.com/livestreet/livestreet/blob/eca10c0186c8174b774a2125d8af3760e1c34825/engine/modules/viewer/plugs/modifier.declension.php
     *
     * @param int $count
     * @param string $forms
     * @param string $lang
     *
     * @return string
     */
    public function declension($count, $forms, $lang = null)
    {
        if (empty($lang)) {
            $lang = app()->getLocale();
        }
        $forms = json_decode($forms, true);

        if ($lang == 'ru') {
            $mod100 = $count % 100;
            switch ($count % 10) {
                case 1:
                    if ($mod100 == 11) {
                        $text = $forms[2];
                    } else {
                        $text = $forms[0];
                    }
                    break;
                case 2:
                case 3:
                case 4:
                    if (($mod100 > 10) && ($mod100 < 20)) {
                        $text = $forms[2];
                    } else {
                        $text = $forms[1];
                    }
                    break;
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                case 0:
                default:
                    $text = $forms[2];
            }
        } else {
            if ($count == 1) {
                $text = $forms[0];
            } else {
                $text = $forms[1];
            }
        }

        return $text;

    }

}
