<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use function abs;

class BorderListener implements Listener {

    public function onMove(PlayerMoveEvent $ev) : void {
        $player = $ev->getPlayer();
        $to = $ev->getTo();
        $x = abs($to->x);
        $z = abs($to->z);
        if($x > 55000 || $z > 55000){
            $ev->cancel();
            UserManager::getInstance()->getOnline($player)->sendTip("§r§8[§bNF§8]\n§r§7You have reached the world border.\n§r§7Please don't continue further.");
            if($x > 55005 || $z > 55005){
                $player->teleport($player->getPosition()->getWorld()->getSpawnLocation());
            }
        }
    }
}