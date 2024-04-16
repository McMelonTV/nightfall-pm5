<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use pocketmine\block\Chest;
use pocketmine\block\tile\Chest as ChestTile;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ConvertcrateCommand extends Executor
{

    public function __construct()
    {
        parent::__construct("convertcrate", "convert a chest to a crate", "/convertcrate <cratetype>", "nightfall.command.convertcrate");
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, "WARNING", "WARNING", "DO NOT USE THIS", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§r§c§l> §r§7Please enter a crate type to convert this chest to.");
            return true;
        }

        $block = $sender->getTargetBlock(10);
        if(!$block instanceof Chest){
            $sender->sendMessage("§r§c§l> §r§7Please look at a chest.");
            return true;
        }

        $pos = $block->getPos();
        $tile = $pos->getWorld()->getTileAt($pos->getX(), $pos->getY(), $pos->getZ());
        if($tile === null){
            $sender->sendMessage("§r§c§l> §r§7Please open the chest once before converting it.");
            return true;
        }

        if(!$tile instanceof ChestTile){
            $sender->sendMessage("§r§c§l> §r§7Please look at a valid chest.");
            return true;
        }

        return true;
    }
}