<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\UserManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class VanishListener implements Listener {

    /**
     * @priority HIGHEST
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();

        foreach(UserManager::getInstance()->getAllOnline() as $t) {
            if($t->getPlayer() === $player) continue;
            if($t->isVanished()) {
                $player->hidePlayer($t->getPlayer());
            }
        }

        $user = UserManager::getInstance()->getOnline($player);
        if(!$user->isVanished()) return;
        Server::getInstance()->removeOnlinePlayer($player);
        $event->setJoinMessage("");
        foreach(UserManager::getInstance()->getAllOnline() as $p) {
            if($p->getPlayer() === $user->getPlayer()) continue;
            $p->getPlayer()->hidePlayer($user->getPlayer());
        }
    }

    public function onLeave(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if($user === null) return;
        if(!$user->isVanished()) return;
        $event->setQuitMessage("");
    }

}