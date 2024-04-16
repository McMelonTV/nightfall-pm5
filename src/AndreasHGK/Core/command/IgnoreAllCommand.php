<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\user\UserManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class IgnoreAllCommand extends Executor{

    public function __construct(){
        parent::__construct("ignoreall", "ignore all chat messages", "/ignoreall", "nightfall.command.ignoreall", ["blockall"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $user = UserManager::getInstance()->getOnline($sender);
        $user->setIgnoreAll(!$user->getIgnoreAll());
        $sender->sendMessage("§r§b§l> §r§7You are ".($user->getIgnoreAll() ? "now" : "no longer")." ignoring all chat.");
        return true;
    }
}