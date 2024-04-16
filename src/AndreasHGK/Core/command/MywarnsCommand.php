<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\warning\Warning;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class MywarnsCommand extends Executor {

    public function __construct(){
        parent::__construct("mywarns", "show your warnings", "/mywarns", "nightfall.command.mywarns");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) return false;

        $user = UserManager::getInstance()->getOnline($sender);

        if(empty($user->getWarnings())) {
            $sender->sendMessage("§r§c§l> §r§7You don't have any warnings!");
            return true;
        }

        $string = "§8§l<--§bNF§8--> ".
            "\n§r§7§7 Your warnings§r";

        foreach($user->getWarnings() as $warning) {
            $string .= "\n §r§8§l> §r§7Staff: §r§b{$warning->getStaffName()} §r§8| §r§7Reason: §r§b{$warning->getReason()}";
            if($warning->isExpired()) $string .= " §r§8| §r§cExpired";
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }

}