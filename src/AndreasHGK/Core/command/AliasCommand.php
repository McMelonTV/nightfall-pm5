<?php

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\Core;
use AndreasHGK\Core\task\AliasTask;
use AndreasHGK\Core\user\BannedUserManager;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class AliasCommand extends Executor{

    public function __construct(){
        parent::__construct("alias", "get alt accounts of a player", "/alias <target>", "nightfall.command.alias");
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) === 0){
            $sender->sendMessage("§r§c§l>§r §7Please enter a player to alias.");
            return true;
        }

        $player = implode(" ", $args);

        $player = Server::getInstance()->getPlayerByPrefix($player);
        if($player === null){
            if(Server::getInstance()->hasOfflinePlayerData($args[0])){
                $player = Server::getInstance()->getOfflinePlayer($args[0]);
            }else{
                $sender->sendMessage("§r§c§l>§r§7 That player has never connected.");
                return true;
            }
        }

        $target = UserManager::getInstance()->get($player);
        $target->getIPList()->remove("172.18.0.1");
        $target = [
            "name" => $target->getName(),
            "iplist" => $target->getIPList()->toArray(),
            "cidlist" => $target->getClientIdList()->toArray(),
            "didlist" => $target->getDeviceIdList()->toArray(),
        ];

        Server::getInstance()->getAsyncPool()->submitTask(new AliasTask($sender, $target, Core::getInstance()->getDataFolder()));

        return true;
    }
}