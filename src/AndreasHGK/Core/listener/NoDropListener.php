<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

class NoDropListener implements Listener {

    public function onDrop(PlayerDropItemEvent $event) : void {
        $player = $event->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if($user->getAdminMode()){
            return;
        }

        $world = $player->getWorld();
        if($world->getFolderName() === "plots"){
            return;
        }
        $player->sendMessage("§r§c§l> §r§7Dropping items is disabled outside of the plotworld.");
        $event->cancel();
    }
}