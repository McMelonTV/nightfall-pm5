<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\mine\MineManager;
use AndreasHGK\Core\mine\RegenerationObserver;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ClearMineCommand extends Executor{

    public function __construct(){
        parent::__construct("clearmine", "clear a mine", "/clearmine <mine>", "nightfall.command.clearmine");
        $this->addParameterMap(0);
        $this->addArrayParameter(0, 0, "mine", "Mine", MineManager::getInstance()->getAllNames(), false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a mine to clear.");
            return true;
        }

        $mine = MineManager::getInstance()->getFromName($args[0]);
        if($mine === null){
            $sender->sendMessage("§r§c§l> §r§7Mine §c".$args[0]."§r§7 could not be found.");
            return true;
        }

        if($mine->isRegenerating()){
            $sender->sendMessage("§r§c§l> §r§7That mine is already regenerating");
            return true;
        }

        RegenerationObserver::getInstance()->addObserver($mine->getId(), $sender->getName());
        $mine->clear();

        $sender->sendMessage("§b§l> §r§7Mine §b".$mine->getName()." §r§7will now start clearing...");
        return true;
    }
}