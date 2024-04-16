<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

final class IntUtils{

    private function __construct(){
        //NOOP
    }

    public static function shortNumber(int $number) : string {
        $count = 1;
        $i = $number;
        while($i >= 1000){
            $i = $i / 1000;
            ++$count;
        }
        switch ($count){
            case 1:
                $str = (string)$number;
                break;
            case 2:
                $str = (string)($i)."K";
                break;
            case 3:
                $str = (string)($i)."M";
                break;
            case 4:
                $str = (string)($i)."B";
                break;
            case 5:
                $str = (string)($i)."T";
                break;
            case 6:
                $str = (string)($i)."Qa";
                break;
            case 7:
                $str = (string)($i)."Qt";
                break;
            case 8:
                $str = (string)($i)."Sx";
                break;
            case 9:
                $str = (string)($i)."Sp";
                break;
            case 10:
                $str = (string)($i)."Oc";
                break;
            case 11:
                $str = (string)($i)."No";
                break;
            case 12:
                $str = (string)($i)."De";
                break;
            default:
                $str = (string)$number;
                break;
        }
        return $str;
    }

    public static function shortNumberRounded(float $number) : string {
        $count = 1;
        $i = $number;
        while($i >= 1000){
            $i = $i / 1000;
            ++$count;
        }
        switch ($count){
            case 1:
                $str = (string)$number;
                break;
            case 2:
                $str = (string)(((int)($i*100))/100)."K";
                break;
            case 3:
                $str = (string)(((int)($i*100))/100)."M";
                break;
            case 4:
                $str = (string)(((int)($i*100))/100)."B";
                break;
            case 5:
                $str = (string)(((int)($i*100))/100)."T";
                break;
            case 6:
                $str = (string)(((int)($i*100))/100)."Qa";
                break;
            case 7:
                $str = (string)(((int)($i*100))/100)."Qt";
                break;
            case 8:
                $str = (string)(((int)($i*100))/100)."Sx";
                break;
            case 9:
                $str = (string)(((int)($i*100))/100)."Sp";
                break;
            case 10:
                $str = (string)(((int)($i*100))/100)."Oc";
                break;
            case 11:
                $str = (string)(((int)($i*100))/100)."No";
                break;
            case 12:
                $str = (string)(((int)($i*100))/100)."De";
                break;
            default:
                $str = (string)$number;
                break;
        }
        return $str;
    }

    public static function toRomanNumerals(int $number) : string {
        $number = abs($number);
        $roman = "";
        while($number > 0){
            switch (true){
                case $number >= 1000:
                    $roman .= "M";
                    $number -= 1000;
                    break;
                case $number >= 900:
                    $roman .= "CM";
                    $number -= 1000;
                    break;
                case $number >= 500:
                    $roman .= "D";
                    $number -= 500;
                    break;
                case $number >= 400:
                    $roman .= "CD";
                    $number -= 400;
                    break;
                case $number >= 100:
                    $roman .= "C";
                    $number -= 100;
                    break;
                case $number >= 90:
                    $roman .= "XC";
                    $number -= 90;
                    break;
                case $number >= 50:
                    $roman .= "L";
                    $number -= 50;
                    break;
                case $number >= 40:
                    $roman .= "XL";
                    $number -= 40;
                    break;
                case $number >= 10:
                    $roman .= "X";
                    $number -= 10;
                    break;
                case $number >= 9:
                    $roman .= "IX";
                    $number -= 9;
                    break;
                case $number >= 5:
                    $roman .= "V";
                    $number -= 5;
                    break;
                case $number >= 4:
                    $roman .= "IV";
                    $number -= 4;
                    break;
                case $number >= 1:
                    $roman .= "I";
                    $number -= 1;
                    break;
            }
        }
        return $roman;
    }

}