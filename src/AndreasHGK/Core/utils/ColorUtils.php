<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;


final class ColorUtils{

    private function __construct(){
        //NOOP
    }

    public const COLORS = [
        "Dark Red" => "4",
        "Red" => "c",
        "Gold" => "6",
        "Yellow" => "e",
        "Dark Green" => "2",
        "Green" => "a",
        "Aqua" => "b",
        "Dark Aqua" => "3",
        "Dark Blue" => "1",
        "Blue" => "9",
        "Light Purple" => "d",
        "Dark Purple" => "5",
        "White" => "f",
        "Gray" => "7",
        "Dark Gray" => "8",
        "Black" => "0",
    ];

    public static function getColorCodeFor(string $color) : string {
        return self::COLORS[$color] ?? "";
    }

    public static function getColorNameFor(string $code) : string {
        return array_search($code, self::COLORS) !== false ? array_search($code, self::COLORS) : "";
    }

    public static function getFullColor(string $name) : string {
        return isset(self::COLORS[$name]) ? "§".self::COLORS[$name].$name : "";
    }

}