<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class WorldCommand extends Executor{

    public function __construct(){
        parent::__construct("world", "transfer worlds", "/world <world>", "nightfall.command.world");
        $this->addParameterMap(0);
        $worlds = [];
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            $worlds[] = $world->getDisplayName();
        }

        $this->addArrayParameter(0, 0, "world", "World", $worlds, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§4§l>§r§7 Please enter a world to transfer to.");
            return true;
        }

        $worldName = (string)$args[0];
        $worldManager = Server::getInstance()->getWorldManager();
        $worldManager->loadWorld($worldName, true);
        $world = $worldManager->getWorldByName($worldName);
        if($world === null){
            $sender->sendMessage("§r§4§l>§r§7 That world does not exist.");
            return true;
        }

        $sender->teleport($world->getSpawnLocation());
        $sender->sendMessage("§r§b§l> §r§7You have been transferred to world §b".$world->getDisplayName()."§r§7.");
        return true;
    }

}