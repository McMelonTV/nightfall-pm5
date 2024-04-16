<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class BlockCommand extends Executor{

    public function __construct(){
        parent::__construct("block", "ignore someone's messages", "/block <player>", "nightfall.command.block", ["ignore"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $senderUser = UserManager::getInstance()->getOnline($sender);
        if(!isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Please enter a target to unblock.");
            return true;
        }

        $pname = implode(" ", $args);
        $target = Server::getInstance()->getPlayerByPrefix($pname);
        if($target === null){
            $target = Server::getInstance()->getOfflinePlayer($pname);
        }

        if(!$target->hasPlayedBefore()){
            $sender->sendMessage("§c§l> §r§7Player with name §c".$pname."§r§7 was never connected.");
            return true;
        }

        if($senderUser->hasBlocked($target->getName())){
            $sender->sendMessage("§c§l> §r§7You have already blocked this user.");
            return true;
        }

        $senderUser->addBlockedUser($target->getName());
        $sender->sendMessage("§r§b§l> §r§7You blocked §b".$target->getName()."§r§7.");
        return true;
    }

}