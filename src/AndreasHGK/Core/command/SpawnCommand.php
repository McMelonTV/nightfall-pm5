<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class SpawnCommand extends Executor{

    public function __construct(){
        parent::__construct("spawn", "teleport to spawn", "/spawn", "nightfall.command.spawn", ["hub"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();

        $vec = $defaultWorld->getSafeSpawn()->add(0.5, 0.5, 0.5);

        $sender->teleport(new Position($vec->getX(), $vec->getY(), $vec->getZ(), $defaultWorld));
        $sender->sendMessage("§b§l> §r§7Teleported to spawn.");
        return true;
    }
}