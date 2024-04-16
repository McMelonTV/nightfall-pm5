<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\mine\MineManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class DisableMineCommand extends Executor{

    public function __construct(){
        parent::__construct("disablemine", "disable a mine", "/disablemine <mine>", "nightfall.command.disablemine");
        $this->addParameterMap(0);
        $this->addArrayParameter(0, 0, "mine", "Mine", MineManager::getInstance()->getAllNames(), false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a mine to disable.");
            return true;
        }

        $mine = MineManager::getInstance()->getFromName(implode($args));
        if($mine === null){
            $sender->sendMessage("§r§c§l> §r§7That mine could not be found.");
            return true;
        }

        $mine->setDisabled(true);
        $sender->sendMessage("§r§b§l> §r§7Mine §b".$mine->getName()."§r§7 is now §bdisabled§7.");
        return true;
    }
}