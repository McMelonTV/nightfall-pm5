<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\block\BlockFactory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetBlockCommand extends Executor{

    public function __construct(){
        parent::__construct("setblock", "set a block", "/setblock <id>", "nightfall.command.setblock");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $pos = $sender->getPosition();
        if(!isset($args[0])){
            $sender->sendMessage("enter an id");
            return true;
        }

        $id = (int)array_shift($args);
        $meta = (int)array_shift($args);

        $sender->sendMessage($pos->__toString());
        $sender->getPosition()->getWorld()->setBlock($pos, BlockFactory::getInstance()->get($id, $meta), false);
        return true;
    }

}