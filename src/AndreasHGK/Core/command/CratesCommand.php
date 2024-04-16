<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class CratesCommand extends Executor{

    public function __construct(){
        parent::__construct("crates", "teleport to the crate room", "/crates", "nightfall.command.crates");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $pos = new Position(1577.5, 24, 626.5, Server::getInstance()->getWorldManager()->getDefaultWorld());
        $sender->teleport($pos);
        $sender->sendMessage("§b§l> §r§7Teleported to the crate room.");
        return true;
    }
}