<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

use function floor;
use function implode;

final class TimeUtils {

    private function __construct(){
        //NOOP
    }

    public static function intToTimeString(int $seconds) : string {
        if($seconds < 0) throw new \UnexpectedValueException("time can't be a negative value");
        if($seconds === 0){
            return "0 seconds";
        }
        $timeString = "";
        $timeArray = [];
        if($seconds >= 86400){
            $unit = floor($seconds/86400);
            $seconds -= $unit*86400;
            $timeArray[] = $unit." days";
        }
        if($seconds >= 3600){
            $unit = floor($seconds/3600);
            $seconds -= $unit*3600;
            $timeArray[] = $unit." hours";
        }
        if($seconds >= 60){
            $unit = floor($seconds/60);
            $seconds -= $unit*60;
            $timeArray[] = $unit." minutes";
        }
        if($seconds >= 1){
            $timeArray[] = $seconds." seconds";
        }
        foreach($timeArray as $key => $value){
            if($key === 0){
                $timeString .= $value;
            }elseif ($key === count($timeArray) - 1){
                $timeString .= " and ".$value;
            }else{
                $timeString .= ", ".$value;
            }
        }
        return $timeString;
    }

    public static function intToShortTimeString(int $seconds) : string {
        if($seconds < 0) throw new \UnexpectedValueException("time can't be a negative value");
        if($seconds === 0){
            return "0s";
        }

        $timeArray = [];
        if($seconds >= 86400){
            $unit = floor($seconds/86400);
            $seconds -= $unit*86400;
            $timeArray[] = $unit."d";
        }
        if($seconds >= 3600){
            $unit = floor($seconds/3600);
            $seconds -= $unit*3600;
            $timeArray[] = $unit."h";
        }
        if($seconds >= 60){
            $unit = floor($seconds/60);
            $seconds -= $unit*60;
            $timeArray[] = $unit."m";
        }
        if($seconds >= 1){
            $timeArray[] = $seconds."s";
        }
        $timeString = implode(" ", $timeArray);
        return $timeString;
    }

    public static function intToAltTimeString(int $seconds) : string{
        if($seconds < 0){
            throw new \UnexpectedValueException("time can't be a negative value");
        }

        if($seconds === 0){
            return "00:00";
        }

        $timeString = "";
        if($seconds >= 60){
            $unit = floor($seconds/60);
            $seconds -= $unit*60;
            if($unit < 10){
                $timeString .= "0";
            }
            $timeString .= (string) $unit;
        }else{
            $timeString .= "00";
        }

        $timeString .= ":";
        if($seconds >= 1){
            if($seconds < 10){
                $timeString .= "0";
            }
            $timeString .= (string) $seconds;
        }else{
            $timeString .= "00";
        }

        return $timeString;
    }

    public static function shortTimeStringToInt(string $string) {
        $array = explode(" ", $string);

        $int = 0;

        foreach($array as $subString){
            switch (!false){
                case strpos(strtolower($subString), "d"):
                    $subString = str_replace("d", "", strtolower($subString));
                    if(!is_numeric($subString)) return false;
                    $int += ((int)$subString)*86400;
                    break;
                case strpos(strtolower($subString), "h"):
                    $subString = str_replace("h", "", strtolower($subString));
                    if(!is_numeric($subString)) return false;
                    $int += ((int)$subString)*3600;
                    break;
                case strpos(strtolower($subString), "m"):
                    $subString = str_replace("m", "", strtolower($subString));
                    if(!is_numeric($subString)) return false;
                    $int += ((int)$subString)*60;
                    break;
                case strpos(strtolower($subString), "s"):
                    $subString = str_replace("s", "", strtolower($subString));
                    if(!is_numeric($subString)) return false;
                    $int += (int)$subString;
                    break;
                default:
                    return false;
            }
        }
        return $int;
    }

}