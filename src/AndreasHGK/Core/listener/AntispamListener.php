<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use function max;
use function min;

class AntispamListener implements Listener {

    public function onChat(PlayerChatEvent $ev) : void{
        $player = $ev->getPlayer();
        $msg = $ev->getMessage();
        $user = UserManager::getInstance()->getOnline($player);
        $user->addSpamScore(10);
        $percent = 0;

        //$similar = similar_text($msg, $user->getLastMessage(), $percent);
        if($msg === $user->getLastMessage()){
            $user->addSpamScore(35);
        }elseif($percent > 0.5){
            $user->addSpamScore(30*($percent/100));
        }

        $time = microtime(true)-$user->getLastMessageTime();
        $user->addSpamScore(50*max(min(1 - $time, 1), 1));

        $user->setSpamScore(min($user->getSpamScore(), 250));
        if($user->getSpamScore() >= 110){
            $ev->cancel();
            $player->sendMessage("§r§c§l> §r§7Please slow down your chat speed!");
            //$user->addCooldownScore(100);
        }

        $user->setLastMessage($msg);
        $user->setLastMessageTime(microtime(true));
    }
}