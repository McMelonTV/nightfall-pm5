<?php

declare(strict_types=1);

namespace AndreasHGK\Core\gang;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;

class GangListener implements Listener {

    public function onChat(PlayerChatEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->isInGang()){
            return;
        }

        $msg = $ev->getMessage();
        $arrayMsg = explode(" ", $msg);
        if($arrayMsg[0] !== ".gc" && $arrayMsg[0] !== ".gangchat"){
            return;
        }

        $ev->cancel();
        array_shift($arrayMsg);
        $newMsg = implode(" ", $arrayMsg);

        $gang = $user->getGang();
        $gangName = $gang->getName();

        $name = $player->getName();
        foreach(UserManager::getInstance()->getAllOnline() as $receiver){
            if($receiver->isInGang() and $receiver->getGang() === $gang){
                $receiver->getPlayer()->sendMessage("§2[".$gangName."] §r§1" . $name . "§r§8: §r§7" . $newMsg . "⛏");
            }
        }

        Server::getInstance()->getLogger()->info("§8[".$gangName."] §4" . $name . "§r§8: §r§7" . $newMsg);
    }
}