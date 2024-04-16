<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\warning\Warning;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class WarningsCommand extends Executor {

    public function __construct(){
        parent::__construct("warnings", "get the warnings for a player", "/warnings <player>", "nightfall.command.warnings");
        $this->addParameterMap(0);

        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Usage: §r§b/warn <player>");
            return true;
        }

        $targetName = array_shift($args);
        $target = Server::getInstance()->getPlayerByPrefix($targetName);
        if($target === null) {
            $sender->sendMessage("§r§c§l> §r§7The provided target has never connected.");
            return true;
        }
        $user = UserManager::getInstance()->getOnline($target);
        if($user === null) {
            $sender->sendMessage("§r§c§l> §r§7The provided target has never connected.");
            return true;
        }

        if(empty($user->getWarnings())) {
            $sender->sendMessage("§r§c§l> §r§7The user does not have any warnings!");
            return true;
        }

        $string = "§8§l<--§bNF§8--> ".
            "\n§r§7§7 §r§b{$target->getName()}§r§7's warnings§r";

        foreach($user->getWarnings() as $id => $warning) {
            $string .= "\n §r§8§l> §r§7ID: §r§b{$id} §r§8| §r§7Staff: §r§b{$warning->getStaffName()} §r§8| §r§7Reason: §r§b{$warning->getReason()}";
            if($warning->isExpired()) $string .= " §r§8| §r§cExpired";
        }

        $sender->sendMessage($string."\n§r§8§l<--++-->⛏");
        return true;
    }

}