<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;

class StaffChatListener implements Listener {

    public function onChat(PlayerChatEvent $ev) : void {
        $player = $ev->getPlayer();
        if(!$player->hasPermission("nightfall.staffchat.send")) {
            return;
        }

        $msg = $ev->getMessage();
        $arrayMsg = explode(" ", $msg);
        if($arrayMsg[0] !== ".sc" && $arrayMsg[0] !== ".staffchat") {
            return;
        }

        $ev->cancel();
        array_shift($arrayMsg);
        $newMsg = implode(" ", $arrayMsg);

        Server::getInstance()->getLogger()->info("§8[STAFF] §4".$player->getName()."§r§8: §r§7".$newMsg);
        foreach(Server::getInstance()->getOnlinePlayers() as $receiver){
            if(!$receiver->hasPermission("nightfall.staffchat.see")) continue;

            $receiver->sendMessage("§8[SC] §r§4".$player->getName()."§r§8: §r§7".$newMsg."⛏");
        }
    }

}