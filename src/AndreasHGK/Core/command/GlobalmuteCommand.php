<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class GlobalmuteCommand extends Executor{

    public function __construct(){
        parent::__construct("globalmute", "toggle global mute", "/globalmute", "nightfall.command.globalmute");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        Core::getInstance()->setGlobalMute(!Core::getInstance()->getGlobalMute());
        if(Core::getInstance()->getGlobalMute()){
            Server::getInstance()->broadcastMessage(str_repeat("\n", 200)."§r§8-----------------------------\n§r§7 Global mute has been turned on.\n§r§8-----------------------------");
        }else{
            Server::getInstance()->broadcastMessage("§r§8-----------------------------\n§r§7 Global mute has been turned off.\n§r§8-----------------------------");
        }

        return true;
    }
}