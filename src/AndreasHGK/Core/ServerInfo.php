<?php

declare(strict_types=1);

namespace AndreasHGK\Core;

final class ServerInfo{

    public static $name = "Nightfall";

    public static $season = "Season 3";

    public static function getVersion() : string{
        return "3.1.1";
    }

    public static function getPatchNotes() : string{
        $str = "§r§bNightfall ".self::$season." §8- §7Release ".self::getVersion();
        $str .= "\n §r§73.1.1";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Shortened Spawn KOTH timer to 10 minutes.";
        $str .= "\n";
        //$str .= "\n §r§8- §r§7Fixed unbreaking not working with auto repair.";
        //$str .= "\n";
        $str .= "\n §r§8- §r§7Fixed `/p clear` replacing bedrock to dirt.";
        $str .= "\n";
        $str .= "\n §r§73.1.0";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Increased all mine sizes (except for A) by a lot.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Added a spawn koth.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Nerfed dust rates for iron, redstone, and diamond blocks.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Added total online time to /stats.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed a dupe with enchants.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed a crash.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed a typo in the rename item form.";
        $str .= "\n";
        $str .= "\n §r§73.0.3";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Extraction is now mythic.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Auto repair is now very rare.";
        $str .= "\n";
        $str .= "\n §r§73.0.2";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Added user's gang in /stats.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Added back the auction house confirm purchase form.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed block lag when mining.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed players not being able to run /ah in pvp.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed XP dropping from blocks in plots.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed two crashes.";
        $str .= "\n";
        $str .= "\n §r§73.0.1";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Fixed missing permissions for warrior (/renameitem).";
        $str .= "\n";
        //$str .= "\n §r§8- §r§7Buffed end stone hardness to 3.75 (was 3.0).";
        //$str .= "\n";
        $str .= "\n §r§8- §r§7Made some performance improvements.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Attempted to fix the multiple votes per vote bug.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Mostly fixed chunk rendering.";
        $str .= "\n";
        $str .= "\n §r§8- §r§7Disabled sub command auto completion temporarily.";
        $str .= "\n";
        $str .= "\n §r§8------";
        $str .= "\n";
        $str .= "\n §r§8It seems like there is no logs of updates before this!";
        return $str;
    }
}