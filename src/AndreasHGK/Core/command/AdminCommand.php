<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AdminCommand extends Executor{

    public function __construct(){
        parent::__construct("admin", "toggle admin mode", "/admin", "nightfall.command.admin", ["adminmode"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player && isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(isset($args[0])){
            $target = Server::getInstance()->getPlayerByPrefix(implode(" ", $args));
            if($target === null){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }
        }else{
            $target = $sender;
        }

        $user = UserManager::getInstance()->get($target);
        if(!$user instanceof User) return true;
        $user->setAdminMode(!$user->getAdminMode());
        if($target !== $sender){
            $sender->sendMessage("§b§l> §r§7You have turned §b".($user->getAdminMode() ? "on" : "off")."§r§7 Admin mode for §b".$target->getName().".");
            $target->sendMessage("§b§l> §r§7Admin mode is now turned §b".($user->getAdminMode() ? "on" : "off")."§r§7.");
        }else{
            $sender->sendMessage("§b§l> §r§7You have turned §b".($user->getAdminMode() ? "on" : "off")."§r§7 Admin mode.");
        }
        return true;
    }

}