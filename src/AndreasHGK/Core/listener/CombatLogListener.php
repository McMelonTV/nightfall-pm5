<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class CombatLogListener implements Listener {

    /**
     * @param PlayerQuitEvent $ev
     *
     * @priority Low
     */
    public function onLeave(PlayerQuitEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if($user === null) {
            return;
        }

        if($user->getLastHit() + 10 >= time()){
            $user->clearLastHit();
            $player->kill();
            //$player->save();
            Server::getInstance()->broadcastMessage("§r§8[§bNF§8] §r§7§b".$player->getName()."§r§7 has been killed for combat logging.");
        }
    }
}
