<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class MessagesListener implements Listener {

    public function onJoin(PlayerJoinEvent $ev) : void {
        if(!$ev->getPlayer()->hasPlayedBefore()){
            $ev->setJoinMessage("§r§8§l[§b+§8]§r§7 Please welcome §b".$ev->getPlayer()->getName()."§r§7 to the server!");
            return;
        }

        $ev->setJoinMessage("§r§8§l[§b+§8]§r §7".$ev->getPlayer()->getName());
    }

    public function onLeave(PlayerQuitEvent $ev) : void {
        $ev->setQuitMessage("§r§8§l[§b-§8]§r §7".$ev->getPlayer()->getName());
    }

    public function onPreLogin(PlayerPreLoginEvent $ev) : void {
        $player = $ev->getPlayerInfo();
        if(!Server::getInstance()->isWhitelisted($player->getUsername()) && Server::getInstance()->hasWhitelist()){
            $ev->setKickReason(PlayerPreLoginEvent::KICK_REASON_SERVER_FULL, "§8[§bNightfall§8]\n§7The server is currently undergoing maintenance.\n§7Contact us at: §bdiscord.nightfall.xyz");
        }

        if(count(Server::getInstance()->getOnlinePlayers()) >= Server::getInstance()->getMaxPlayers()){
            $ev->setKickReason(PlayerPreLoginEvent::KICK_REASON_SERVER_FULL, "§8[§bNightfall§8]\n§7The server is currently full.");
        }
    }
}