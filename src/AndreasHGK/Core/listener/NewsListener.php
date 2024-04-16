<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\ServerInfo;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class NewsListener implements Listener {

    /**
     * @param PlayerJoinEvent $ev
     * @priority HIGHEST
     */
    public function onJoin(PlayerJoinEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if($user->getLastPatchNotes() !== ServerInfo::getVersion()){
            $player->sendMessage("§r§7There was a new patch while you were offline. Do §b/news §r§7to view the patchnotes.");
        }
    }
}