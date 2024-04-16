<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CommandSpyCommand extends Executor{

    public function __construct(){
        parent::__construct("commandspy", "see what commands people type", "/commandspy", "nightfall.command.commandspy", ["cspy"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player && !isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $user = UserManager::getInstance()->get($sender);
        if($user->getCommandSpy()){
            $str = "disabled";
        }else{
            $str = "enabled";
        }

        $user->setCommandSpy(!$user->getCommandSpy());
        $sender->sendMessage("§b§l>§r§7 Commandspy is now §b".$str."§7.");
        return true;
    }
}