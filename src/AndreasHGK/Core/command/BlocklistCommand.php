<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class BlocklistCommand extends Executor{

    public function __construct(){
        parent::__construct("blocklist", "check the list of users you blocked", "/blocklist [player]", "nightfall.command.blocklist", ["ignorelist"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
        $this->addParameterMap(1, "nightfall.command.admin");
        $this->addNormalParameter(1, 0, "message", CustomCommandParameter::ARG_TYPE_STRING, true, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        $senderUser = UserManager::getInstance()->getOnline($sender);
        if(isset($args[0]) && $senderUser->getAdminMode()){
            $targetName = implode(" ", $args);
            $target = Server::getInstance()->getPlayerByPrefix($targetName);
            if($target === null){
                $sender->sendMessage("§c§l> §r§7That player was not found.");
                return true;
            }

            $user = UserManager::getInstance()->getOnline($target);
        }else{
            $target = $sender;
            $user = $senderUser;
        }

        if(empty($user->getBlockedUsers())){
            $sender->sendMessage($user === $senderUser ? "§r§c§l> §r§7You haven't blocked anyone." : "§r§c§l> §r§b".$target->getName()." hasn't blocked anyone.");
            return true;
        }

        $string = "§8§l<--§bNF§8--> ".
            ($user === $senderUser ? "\n§r§7§7 Your list of blocked users§r" : "\n§r§7§b ".$target->getName()."§7's list of blocked users§r");

        foreach($user->getBlockedUsers() as $blockedUser){
            $string .= "\n§r§8 > §r§b".$blockedUser;
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }

}