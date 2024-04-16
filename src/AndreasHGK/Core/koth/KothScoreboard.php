<?php

declare(strict_types=1);

namespace AndreasHGK\Core\koth;

use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\TimeUtils;
use JackMD\ScoreFactory\ScoreFactory;
use pocketmine\player\Player;
use function intdiv;

final class KothScoreboard{

    public static function update(Player $player) : void{
        if(ScoreFactory::hasScore($player)){
            ScoreFactory::removeScore($player);
        }

        $running = KothManager::getInstance()->getRunning();
        $time = TimeUtils::intToAltTimeString(intdiv($running->getTimeLeft(), 2));

        ScoreFactory::setScore($player, "§r§8[§l§aKOTH§r§8]");
        $lines = [
            0 => "§7 ",
            1 => "  §r§7" . $running->getName() . ": §r§a$time   ",
            2 => "§7",
            3 => "",
        ];

        if(($capper = $running->getCapper()) !== null){
            $user = UserManager::getInstance()->getOnline($capper);
            if($user !== null){
                $lines[2] = "  §r§7Capping: §r§a" . $capper->getName() . "   ";
                $lines[3] = "  §r§7Gang: §r§a".$user->getGang()->getName() . "   ";
            }
        }

        ScoreFactory::setScoreLines($player, $lines);
    }
}