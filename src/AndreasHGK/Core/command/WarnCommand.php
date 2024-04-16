<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\warning\Warning;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class WarnCommand extends Executor {

    public function __construct(){
        parent::__construct("warn", "warn a player", "/warn <player> <reason>", "nightfall.command.warn");
        $this->addParameterMap(0);

        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, false);
        $this->addNormalParameter(0, 1, "reason", CustomCommandParameter::ARG_TYPE_STRING, false, false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Usage: §r§b/warn <player> <reason>");
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

        if(!isset($args[0])) {
            $sender->sendMessage("§r§c§l>§r§7 Usage: §r§b/warn <player> <reason>");
            return true;
        }

        $reason = implode(" ", $args);
        $warning = new Warning($target, time(), $reason, $sender->getName());

        $user->warn($warning);

        $sender->sendMessage("§r§b§l> §r§7You warned §r§b{$target->getName()} §r§7with reason §r§b{$warning->getReason()}§r§7.");
        return true;
    }

}