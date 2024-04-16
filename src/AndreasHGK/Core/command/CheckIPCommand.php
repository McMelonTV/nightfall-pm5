<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\User;
use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class CheckIPCommand extends Executor{

    public function __construct(){
        parent::__construct("checkip", "check someone's IP's", "/checkip <player>", "nightfall.command.checkip", ["iplist"]);
        $this->addParameterMap(0);
        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§c§l> §r§7Please enter a target to check.");
            return true;
        }

        $pname = implode(" ", $args);
        $target = Server::getInstance()->getOfflinePlayer($pname);
        if(!$target->hasPlayedBefore()){
            $sender->sendMessage("§c§l> §r§7Player with name §c".$pname."§r§7 was never connected.");
            return true;
        }

        $user = UserManager::getInstance()->get($target);
        if(empty($user->getIPList())){
            $sender->sendMessage("§c§l> §r§7Player §c".$pname."§r§7 has no IPs on record.");
            return true;
        }

        $string = "§8§l<--§bNF§8--> "."\n§r§7§7 §b".$target->getName()."§r§7's IP list";
        if($user instanceof User){
            $string .= "\n§r §8§l> §r§7Current ip: §b".$user->getPlayer()->getNetworkSession()->getIp();
        }

        foreach ($user->getIPList() as $ip){
            $string .= "\n§r §8§l> §r§7".$ip;
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }
}