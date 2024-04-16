<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class ReplyCommand extends Executor{

    public function __construct(){
        parent::__construct("reply", "reply to a private message", "/r <message>", "nightfall.command.tell", ["r"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "message", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(count($args) < 1) {
            $sender->sendMessage("§r§c§l> §r§7Please enter a message to send.");
            return true;
        }

        $user = UserManager::getInstance()->get($sender);
        if(!$user instanceof User) {
            return true;
        }

        if($user->isMuted()){
            $sender->sendMessage("§r§c§l> §r§7You are muted!");
            return true;
        }

        $targetName = $user->getLastMsgSender();
        if($targetName === null){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $target = Server::getInstance()->getPlayerExact($targetName);
        if($target === null){
            $sender->sendMessage("§c§l> §r§7The target player was not found.");
            return true;
        }

        $targetUser = UserManager::getInstance()->get($target);
        if($targetUser->hasBlocked($sender->getName()) && !$user->getAdminMode()){
            $sender->sendMessage("§c§l> §r§7You are unable to send messages to this player.");
            return true;
        }

        if($targetUser instanceof User) {
            if($targetUser->isAfk()){
                $sender->sendMessage("§c§l> §r§7The target player is currently AFK.");
            }

            $targetUser->setLastMsgSender($sender->getName());
        }

        $message = implode(" ", $args);

        $sender->sendMessage("§8[§r§bYou§7->§b".$target->getDisplayName()."§r§8]§r §7".$message);
        $name = $sender instanceof Player ? $sender->getDisplayName() : $sender->getName();
        $target->sendMessage("§8[§r§b".$name."§7->§bYou§r§8]§r §7".$message);
        return true;
    }

}