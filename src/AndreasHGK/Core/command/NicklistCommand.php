<?php

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NicklistCommand extends Executor{

    public function __construct(){
        parent::__construct("nicklist", "list of online players nicks", "/nicklist", "nightfall.command.nicklist");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $str = "§8§l<--§bNF§8--> "."\n§r§7 Nick list";
        foreach(UserManager::getInstance()->getAllOnline() as $user){
            if($user->isVanished()) continue;
            if(!$user->hasNick()){
                continue;
            }

            $str .= "\n§r§b" . $user->getName() . " §r§7->§b " . $user->getNick();
        }

        $sender->sendMessage(TextFormat::colorize($str."\n§r§8§l<--++-->⛏"));

        return true;
    }
}