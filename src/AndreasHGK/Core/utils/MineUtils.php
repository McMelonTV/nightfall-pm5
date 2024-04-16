<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

final class MineUtils {

    private function __construct(){
        //NOOP
    }

    public static function getPrestigePrice(int $prestige) : int {
        return (int)(2500000+(($prestige-2)*2500000*0.6));
    }

    public static function getPrestigeReward(int $prestige) : int {
        //return 10000+(($prestige-2)*2000);
        return 10000;
    }

}