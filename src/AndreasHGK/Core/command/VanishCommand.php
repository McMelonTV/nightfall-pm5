<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class VanishCommand extends Executor {

    public function __construct(){
        parent::__construct("vanish", "toggle vanish mode", "/vanish", "nightfall.command.vanish", []);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $user = UserManager::getInstance()->get($sender);
        if($user->isVanished()){
            $str = "disabled";
        }else{
            $str = "enabled";
        }

        $user->setVanished(!$user->isVanished());
        if($user->isVanished()) {
            foreach(UserManager::getInstance()->getAllOnline() as $p) {
                if($p->getPlayer() === $sender) continue;
                $p->getPlayer()->hidePlayer($sender);
            }
            Server::getInstance()->broadcastMessage("§r§8§l[§b-§8]§r §7".$sender->getName());
        }else{
            foreach(UserManager::getInstance()->getAllOnline() as $p) {
                if($p->getPlayer() === $sender) continue;
                $p->getPlayer()->showPlayer($sender);
            }
            Server::getInstance()->broadcastMessage("§r§8§l[§b+§8]§r §7".$sender->getName());
        }

        $sender->sendMessage("§b§l>§r§7 Vanish is now §b".$str."§7.");
        return true;
    }

}