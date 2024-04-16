<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\task\ConvertWorldTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ConvertWorldCommand extends Executor{

    public function __construct(){
        parent::__construct("convertworld", "convert a world", "/convertworld", "nightfall.command.convertworld");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, "WARNING", "WARNING", "DO NOT USE THIS", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $sender->sendMessage("§cCONVERTING CURRENT WORLD");
        $task = new ConvertWorldTask($sender->getWorld());
        Core::getInstance()->getScheduler()->scheduleTask($task);
        return true;
    }
}