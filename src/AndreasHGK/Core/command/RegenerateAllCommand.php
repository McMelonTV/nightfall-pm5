<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\mine\MineManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class RegenerateAllCommand extends Executor{

    public function __construct(){
        parent::__construct("regenerateall", "regenerate all mines", "/regenerateall", "nightfall.command.regenerateall", ["regenall"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        MineManager::getInstance()->regenAll();
        return true;
    }

}