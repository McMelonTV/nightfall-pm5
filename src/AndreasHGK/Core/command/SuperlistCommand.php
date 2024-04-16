<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class SuperlistCommand extends Executor{

    public function __construct(){
        parent::__construct("superlist", "list ALL the online users", "/superlist", "nightfall.command.superlist", []);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $normalPlayers = [];
        $staffPlayers = [];
        $vanishedPlayers = [];
        foreach(UserManager::getInstance()->getAllOnline() as $user){
            if($user->isVanished()) {
                $vanishedPlayers[] = $user->getPlayer()->getName();
            }elseif($user->getRank()->getRank()->isStaff()){
                $staffPlayers[] = $user->getPlayer()->getName();
            }else{
                $normalPlayers[] = $user->getPlayer()->getName();
            }
        }

        $string = "§8§l<--§bNF§8--> "."\n§r§7 Nightfall online player list §r§8(".(count($normalPlayers)+count($staffPlayers)+count($vanishedPlayers))."/".Server::getInstance()->getMaxPlayers()." online)§r";
        if(count($normalPlayers) > 0) {
            $string .= "\n §r§8§l> §r§7§b".count($normalPlayers)." §7players: §b".(implode("§r§7, §r§b", $normalPlayers));
        }

        if(count($staffPlayers) > 0) {
            $string .= "\n §r§8§l> §r§7§b".count($staffPlayers)." §7staff members: §b".implode("§r§7, §r§b", $staffPlayers);
        }
        if(count($vanishedPlayers) > 0) {
            $string .= "\n §r§8§l> §r§7§b".count($vanishedPlayers)." §7vanished players: §b".implode("§r§7, §r§b", $vanishedPlayers);
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");

        return true;
    }
}