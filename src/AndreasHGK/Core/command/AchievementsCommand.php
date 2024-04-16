<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\ui\AchievementsInventory;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AchievementsCommand extends Executor{

    public function __construct(){
        parent::__construct("achievements", "view your achievements", "/achievements", "nightfall.command.achievements", ["adminmode"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage("§c§l> §r§7Sender needs to be a player.");
            return true;
        }

        AchievementsInventory::sendTo($sender);
        $sender->sendMessage("§b§l> §r§7Showing your achievements...");
        return true;
    }
}