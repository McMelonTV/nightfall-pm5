<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AFKCommand extends Executor{

    public function __construct(){
        parent::__construct("afk", "toggle your 'away from keyboard' status", "/afk", "nightfall.command.afk");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $user = UserManager::getInstance()->get($sender);
        if(!$user instanceof User) {
            return true;
        }

        if($user->isAFK()) {
            $user->activity = true;
            $user->setAFK(false);
            $sender->sendMessage("§r§b§l>§r§7 You are no longer AFK.");
            return true;
        }

        $user->activity = false;
        $user->setAFK();
        $sender->sendMessage("§r§b§l>§r§7 You are now AFK.");
        return true;
    }

}