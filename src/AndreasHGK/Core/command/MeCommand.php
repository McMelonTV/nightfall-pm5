<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MeCommand extends Executor{

    public function __construct(){
        parent::__construct("me", "Performs the specified action in chat", "/me <action>", "nightfall.command.me");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "action", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter an action to perform in chat.");
            return true;
        }

        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $player->sendMessage("§r§8* §r§b".$sender->getName()."§r§7 ".TextFormat::clean(implode(" ", $args))."⛏");
        }

        return true;
    }

}