<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\BannedUserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class UnbanCommand extends Executor{

    public function __construct(){
        parent::__construct("unban", "unban a banned player", "/unban <player>", "nightfall.command.unban", ["pardon"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "player", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) < 1){
            $sender->sendMessage("§r§c§l> §r§7Please enter a player to unban.");
            return true;
        }

        $target = implode(" ", $args);
        if(!BannedUserManager::getInstance()->isBanned($target)){
            $sender->sendMessage("§r§c§l> §r§7The selected user is not banned.");
            return true;
        }

        BannedUserManager::getInstance()->unban($target);
        $sender->sendMessage("§r§c§l> §r§7You have unbanned §b".$target.".");
        return true;
    }

}