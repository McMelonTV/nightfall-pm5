<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\AutoComplete\parameter\CustomCommandParameter;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\warning\Warning;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;

class ClearwarnCommand extends Executor {

    public function __construct(){
        parent::__construct("clearwarn", "clear a warning from a player", "/clearwarn <player> <warning>", "nightfall.command.clearwarn");
        $this->addParameterMap(0);

        $this->addNormalParameter(0, 0, "target", CustomCommandParameter::ARG_TYPE_TARGET, false, false);
        $this->addNormalParameter(0, 1, "warning", CustomCommandParameter::ARG_TYPE_INT, false, false);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l>§r§7 Usage: §r§b/warn <player> <warning>");
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
            $sender->sendMessage("§r§c§l>§r§7 Usage: §r§b/warn <player> <warning>");
            return true;
        }

        if(!is_numeric($args[0])) {
            $sender->sendMessage("§r§c§l>§r§7 Usage: §r§b/warn <player> <warning>");
            return true;
        }
        $warningInt = (int)$args[0];
        $warns = $user->getWarnings();
        $warning = $warns[$warningInt] ?? null;
        if($warning === null) {
            $sender->sendMessage("§r§c§l>§r§7 The provided player does not have a warning with this ID.");
            return true;
        }
        unset($warns[$warningInt]);
        $user->setWarnings($warns);

        $sender->sendMessage("§r§b§l> §r§7You cleared warning §r§b#{$warningInt} §r§7with reason §r§b{$warning->getReason()} §r§7from §r§b{$target->getName()}§r§7.");
        return true;
    }

}