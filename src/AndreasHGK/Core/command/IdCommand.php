<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class IdCommand extends Executor{

    public function __construct(){
        parent::__construct("id", "see an id", "/id", "nightfall.command.id");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;

        $target = $sender->getTargetBlock(20);
        if($target === null){
            $sender->sendMessage("§r§c§l> §r§7Block not found.");
            return true;
        }

        $sender->sendMessage("§r§b§l> §r§7Block id:§c".$target->getId()."§7 meta:§c".$target->getMeta());
        return true;
    }

}