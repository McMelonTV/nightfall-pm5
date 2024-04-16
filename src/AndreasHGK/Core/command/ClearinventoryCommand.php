<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class ClearinventoryCommand extends Executor{

    public function __construct(){
        parent::__construct("clearinventory", "clear someones inventory", "/clearinventory <player>", "nightfall.command.clearinventory", ["clearinv"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Please enter a target.");
            return true;
        }

        $pname = implode(" ", $args);
        $target = Server::getInstance()->getPlayerByPrefix($pname);
        if($target === null){
            $sender->sendMessage("§c§l> §r§7That player could not be found.");
            return true;
        }

        $target->getInventory()->clearAll();

        $sender->sendMessage("§r§b§l> §r§7You cleared §b".$target->getName()."§r§7's inventory.");
        return true;
    }
}