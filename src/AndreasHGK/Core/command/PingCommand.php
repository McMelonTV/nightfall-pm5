<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class PingCommand extends Executor{

    public function __construct(){
        parent::__construct("ping", "check your ping", "/ping", "nightfall.command.ping");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(isset($args[0])){
            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if($player === null){
                $sender->sendMessage("§c§l> §r§7That player was never connected.");
                return true;
            }

            $sender->sendMessage("§r§b§l> §r§b{$player->getName()}§7's ping: §b".$player->getNetworkSession()->getPing()."§r§7.");
            return true;
        }

        if(!$sender instanceof Player) {
            return false;
        }

        $sender->sendMessage("§r§b§l> §r§7Your ping: §b".$sender->getNetworkSession()->getPing()."§r§7.");
        return true;
    }
}