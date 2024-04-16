<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ClearlagCommand extends Executor{

    public function __construct(){
        parent::__construct("clearlag", "clear the fucking lag ffs", "/clearlag", "nightfall.command.clearlag");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $sender->sendMessage("§b§l> §r§7Cleared §b".Core::getInstance()->clearItemEntities()." §r§7items.");
        return true;
    }
}