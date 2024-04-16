<?php

declare(strict_types=1);

namespace AndreasHGK\Core\listener;

use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\player\OfflinePlayer;

class AFKListener implements Listener {

    public function onBreak(BlockBreakEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->get($player);
        if(!$user instanceof User) {
            return;
        }

        $user->activity = true;
        if($user->isAFK()){
            $user->setAFK(false);
            $player->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
        }
    }

    public function onInteract(PlayerInteractEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->get($player);
        if(!$user instanceof User) {
            return;
        }

        $user->activity = true;
        if($user->isAFK()){
            $user->setAFK(false);
            $player->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
        }
    }

    public function onMove(PlayerMoveEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);

        $user->activity = true;
        if($user->isAFK()){
            $user->setAFK(false);
            $player->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
        }
    }

    public function onChat(PlayerChatEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);

        $user->activity = true;
        if($user->isAFK()){
            $user->setAFK(false);
            $player->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
        }

        $msg = explode(" ", $ev->getMessage());
        foreach (UserManager::getInstance()->getAllOnline() as $user){
            $userPlayer = $user->getPlayer();
            if($userPlayer === null || $userPlayer instanceof OfflinePlayer) {
                continue;
            }

            if(!$user->isAFK()) {
                continue;
            }

            if(in_array("@".$userPlayer->getDisplayName(), $msg) || in_array("@".$userPlayer->getName(), $msg)){
                $player->sendMessage("§r§b§l>§r§7 Player §b".$user->getName()." §r§7 is currently afk.");
            }
        }
    }

    public function onCommand(PlayerCommandPreprocessEvent $ev) : void{
        if(explode(" ", $ev->getMessage())[0] === "/afk") {
            return;
        }

        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);

        $user->activity = true;
        if($user->isAFK()){
            $user->setAFK(false);
            $player->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
        }
    }

    public function onSneak(PlayerToggleSneakEvent $ev) : void{
        $player = $ev->getPlayer();
        $user = UserManager::getInstance()->getOnline($player);

        $user->activity = true;
        if($user->isAFK()){
            $user->setAFK(false);
            $player->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
        }
    }
}