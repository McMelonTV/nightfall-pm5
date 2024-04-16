<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class GlobalmuteListener implements Listener{

    /**
     * @param PlayerChatEvent $ev
     *
     * @priority LOW
     */
    public function onChat(PlayerChatEvent $ev) : void {
        if(!Core::getInstance()->getGlobalMute()) {
            return;
        }

        $user = UserManager::getInstance()->getOnline($ev->getPlayer());
        if($user->getAdminMode()) {
            return;
        }

        $ev->cancel();
    }
}