<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class SudoCommand extends Executor{

    public function __construct(){
        parent::__construct("sudo", "execute something as someone else", "/sudo <player> <command|message>", "nightfall.command.sudo");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "message", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§b§l> §r§7Please enter a target.");
            return true;
        }

        $target = Server::getInstance()->getPlayerByPrefix(array_shift($args));
        if($target === null){
            $sender->sendMessage("§b§l> §r§7That player was not found.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§b§l> §r§7Please enter a command or message to send.");
            return true;
        }

        $command = implode(" ", $args);

        $sender->sendMessage("§b§l> §r§7Executing command...");
        $target->chat($command);
        return true;
    }

}