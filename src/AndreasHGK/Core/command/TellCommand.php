<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TellCommand extends Executor{

    public function __construct(){
        parent::__construct("tell", "send a private message", "/tell <player> <message>", "nightfall.command.tell", ["w", "msg"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
        $this->addNormalParameter(0, 1, "message", CustomCommandParameter::ARG_TYPE_STRING, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(count($args) < 1){
            $sender->sendMessage("§r§c§l> §r§7Please enter a player to send your message to.");
            return true;
        }elseif(count($args) < 2){
            $sender->sendMessage("§r§c§l> §r§7Please enter a message to send.");
            return true;
        }

        $username = array_shift($args);
        $player = $sender->getServer()->getPlayerByPrefix($username);
        if($sender instanceof Player){
            $user = UserManager::getInstance()->getOnline($sender);
            if($user->isMuted()) {
                $sender->sendMessage("§r§c§l> §r§7You are muted!");
                return true;
            }
        }

        if($player === $sender){
            $sender->sendMessage("§r§c§l> §r§7You can't send a message to yourself!");
            return true;
        }

        if($player instanceof Player){
            $targetUser = UserManager::getInstance()->get($player);
            if($sender instanceof Player && $targetUser->hasBlocked($sender->getName()) && !$user->getAdminMode()){
                $sender->sendMessage("§c§l> §r§7You are unable to send messages to this player.");
                return true;
            }

            if($targetUser instanceof User) {
                if($targetUser->isAfk()){
                    $sender->sendMessage("§c§l> §r§7The target player is currently AFK.");
                }

                if($sender instanceof Player){
                    $targetUser->setLastMsgSender($sender->getName());
                }
            }

            $message = implode(" ", $args);
            $sender->sendMessage("§8[§r§bYou§7->§b".$player->getDisplayName()."§r§8]§r §7".$message);
            $name = $sender instanceof Player ? $sender->getDisplayName() : $sender->getName();
            $player->sendMessage("§8[§r§b".$name."§7->§bYou§r§8]§r §7".$message);
        }else{
            $sender->sendMessage("§r§c§l> §r§7Player §b".$username."§r§7 was not found.");
        }

        return true;
    }

}