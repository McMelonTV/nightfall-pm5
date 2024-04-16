<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class ListCommand extends Executor{

    public function __construct(){
        parent::__construct("list", "list the online users", "/list", "nightfall.command.list", ["players"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $normalPlayers = [];
        $staffPlayers = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if(!$sender instanceof Player || $sender->canSee($player)){
                if(UserManager::getInstance()->get($player)->getRank()->getRank()->isStaff()){
                    $staffPlayers[] = $player->getName();
                }else{
                    $normalPlayers[] = $player->getName();
                }
            }
        }

        $string = "§8§l<--§bNF§8--> "."\n§r§7 Nightfall online player list §r§8(".(count($normalPlayers)+count($staffPlayers))."/".Server::getInstance()->getMaxPlayers()." online)§r";
        if(count($normalPlayers) > 0) {
            $string .= "\n §r§8§l> §r§7§b".count($normalPlayers)." §7players: §b".(implode("§r§7, §r§b", $normalPlayers));
        }

        if(count($staffPlayers) > 0) {
            $string .= "\n §r§8§l> §r§7§b".count($staffPlayers)." §7staff members: §b".implode("§r§7, §r§b", $staffPlayers);
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");

        return true;
    }
}