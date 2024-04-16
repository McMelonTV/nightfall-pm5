<?php

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;

class GappleListener implements Listener {

    public function onItemConsume(PlayerItemConsumeEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);

        if($user->hasCooldownFor("gapple", 15)){
            $ev->cancel();

            $s = (int)floor($user->getCooldownFor("gapple", 15));

            $player->sendTip("§c§l> §r§7Please wait $s seconds before consuming another gapple");

            return;
        }

        $user->setCooldownFor("gapple");
    }
}