<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MyCoordsCommand extends Executor{

    public function __construct(){
        parent::__construct("mycoords", "a debug command", "/mycoords", "nightfall.command.mycoords");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $sender->sendMessage($sender->getPosition()->__toString());
        return true;
    }

}