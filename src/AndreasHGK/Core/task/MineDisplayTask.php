<?php

declare(strict_types=1);

namespace AndreasHGK\Core\task;

use AndreasHGK\Core\pvp\PVPZoneManager;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\IntUtils;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MineDisplayTask extends Task {

    public $players = [];

    public function getInterval() : int {
        return 60;
    }

    public function onRun() : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $location = $player->getLocation();
            if(PVPZoneManager::getInstance()->isPVPZone($location->x, $location->y, $location->z, $player->getWorld())){
                $player->setScoreTag("§r§7".$player->getHealth()."§c❤");
            }else{
                $user = UserManager::getInstance()->get($player);
                $mine = $user->getMineRank()->getTag();
                $player->setScoreTag(TextFormat::colorize("§r§7".IntUtils::toRomanNumerals($user->getPrestige())."⛏§r§8|⛏§r".$mine));
            }
        }
    }
}
