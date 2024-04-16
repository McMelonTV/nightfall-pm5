<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class SetSpawnCommand extends Executor{

    public function __construct(){
        parent::__construct("setspawn", "set the spawn", "/setspawn", "nightfall.command.setspawn");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $world = $sender->getWorld();

        $world->setSpawnLocation($sender->getPosition());
        Server::getInstance()->getWorldManager()->setDefaultWorld($world);
        $sender->sendMessage("§b§l> §r§7Set the server spawn to your current position.");
        return true;
    }
}