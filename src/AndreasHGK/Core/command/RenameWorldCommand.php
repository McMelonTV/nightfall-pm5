<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class RenameWorldCommand extends Executor{

    public function __construct(){
        parent::__construct("renameworld", "rename a world", "/renameworld <world>", "nightfall.command.renameworld");
        $this->addParameterMap(0);
        $worlds = [];
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            $worlds[] = $world->getDisplayName();
        }

        $this->addArrayParameter(0, 0, "world", "World", $worlds, false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            $sender->sendMessage("§r§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a new name for the world you're in.");
            return true;
        }

        $name = implode(" ", $args);
        $world = $sender->getWorld();

        $world->getProvider()->getWorldData()->setString("LevelName", $name);

        $sender->sendMessage("§r§b§l> §r§7Changed the current level's name to §b".$name."§r§7.");
        return true;
    }
}