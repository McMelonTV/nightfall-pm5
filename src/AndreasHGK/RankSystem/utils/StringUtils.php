<?php

declare(strict_types=1);

namespace AndreasHGK\RankSystem\utils;

class StringUtils {

    private function __construct() {}

    /**
     * Convert an amount of seconds to a duration written in text
     *
     * @param int $seconds
     * @param bool $short whether or not to use short format (ex.: 5d 3h) instead of the long format (ex.: 5 days and 3 hours)
     * @return string
     */
    public static function intToTimeString(int $seconds, bool $short = true) : string {
        if($seconds < 0) throw new InvalidArgumentException("time can't be a negative value");
        if($seconds === 0) {
            return "0 seconds";
        }
        $timeString = "";
        $timeArray = [];
        if($seconds >= 86400) {
            $unit = floor($seconds / 86400);
            $seconds -= $unit * 86400;
            $timeArray[] = $unit . ($short ? "d" : " days");
        }
        if($seconds >= 3600) {
            $unit = floor($seconds / 3600);
            $seconds -= $unit * 3600;
            $timeArray[] = $unit . ($short ? "h" : " hours");
        }
        if($seconds >= 60) {
            $unit = floor($seconds / 60);
            $seconds -= $unit * 60;
            $timeArray[] = $unit . ($short ? "m" : " minutes");
        }
        if($seconds >= 1) {
            $timeArray[] = $seconds . ($short ? "s" : " seconds");
        }

        if($short) {
            $timeString = implode(" ", $timeArray);
        }else{
            foreach($timeArray as $key => $value) {
                if($key === 0) {
                    $timeString .= $value;
                } elseif($key === count($timeArray) - 1) {
                    $timeString .= " and " . $value;
                } else {
                    $timeString .= ", " . $value;
                }
            }
        }
        return $timeString;
    }

}