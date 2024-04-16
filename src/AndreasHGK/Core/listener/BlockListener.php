<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class BlockListener implements Listener {

    public function onChat(PlayerChatEvent $ev) : void {
        $player = $ev->getPlayer();

        $recipients = $ev->getRecipients();
        foreach(UserManager::getInstance()->getAllOnline() as $tUser){
            if($tUser->hasBlocked($player->getName()) || $tUser->getIgnoreAll()){
                //unset($recipients[array_search($tPlayer, $recipients)]);
                unset($recipients[spl_object_id($tUser->getPlayer())]);
            }
        }

        $ev->setRecipients($recipients);
    }
}