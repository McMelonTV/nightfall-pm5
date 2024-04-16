<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\player\Player;

class DelayedCommandListener implements Listener {

    /**
     * @param PlayerMoveEvent $ev
     *
     * @priority High
     */
    public function onMove(PlayerMoveEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        $from = $ev->getFrom();
        $to = $ev->getTo();
        if($from->x === $to->x && $from->y === $to->y && $from->z === $to->z) {
            return;
        }

        if($user->isWaitingForCommand()){
            $user->setWaitingforCommand(false);

            $user->cancelCommandDelayTask();
            $player->sendMessage("§r§c§l> §r§7You have cancelled the command!");
        }
    }

    /**
     * @param EntityDamageEvent $ev
     *
     * @priority High
     */
    public function onDamage(EntityDamageEvent $ev) : void {
        $player = $ev->getEntity();
        if(!$player instanceof Player) {
            return;
        }

        $user = UserManager::getInstance()->getOnline($player);
        if($user === null) return;
        if($user->isWaitingForCommand()){
            $user->setWaitingforCommand(false);

            $user->cancelCommandDelayTask();
            $player->sendMessage("§r§c§l> §r§7You have cancelled the command!");
        }
    }

    /**
     * @param PlayerToggleSneakEvent $ev
     *
     * @priority High
     */
    public function onSneak(PlayerToggleSneakEvent $ev) : void {
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);
        if($user === null) {
            return;
        }

        if($user->isWaitingForCommand()){
            $user->setWaitingforCommand(false);

            $user->cancelCommandDelayTask();
            $player->sendMessage("§r§c§l> §r§7You have cancelled the command!");
        }
    }
}