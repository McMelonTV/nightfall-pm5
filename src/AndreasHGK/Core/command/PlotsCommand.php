<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\plot\PlotManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\player\Player;

class PlotsCommand extends Executor{

    public function __construct(){
        parent::__construct("plots", "teleport to the plotworld", "/plots", "nightfall.command.plots", ["plotworld"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $sender->teleport(new Location(0.5, 65, 0.5, 0, 0, PlotManager::getInstance()->getWorld()));
        $sender->sendMessage("§r§b§l> §r§7You have been teleported to the plots.");
        return true;
    }

}