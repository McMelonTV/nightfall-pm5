<?php

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class ClearEntitiesCommand extends Executor{

    public function __construct(){
        parent::__construct("clearentities", "clear item entities", "/clearentities", "nightfall.command.clearentities", ["clearlag", "clearentity"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§r§c§l>§r§7 Please execute this command ingame.");
            return true;
        }

        Server::getInstance()->broadcastMessage("§r§8[§bNF§8]§r§7 Now clearing all item entities...");
        Core::getInstance()->clearItemEntities();
        return true;
    }
}