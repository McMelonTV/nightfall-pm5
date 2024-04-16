<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class SayCommand extends Executor{

    public function __construct(){
        parent::__construct("say", "say something", "/say <message>", "nightfall.command.say");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "message", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) < 1) {
            $sender->sendMessage("§r§c§l> §r§7Please enter a message.");
            return true;
        }

        $msg = implode(" ", $args);

        Server::getInstance()->broadcastMessage("§r§8[§b".$sender->getName()."§r§8] §r§7".$msg);
        return true;
    }
}