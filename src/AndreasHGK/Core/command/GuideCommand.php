<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class GuideCommand extends Executor{

    public function __construct(){
        parent::__construct("guide", "get the guide book", "/guide", "nightfall.command.guide", ["guidebook"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $item = CustomItemManager::getInstance()->get(CustomItem::GUIDEBOOK);
        $sender->getInventory()->addItem($item->getItem());

        $sender->sendMessage("§r§b§l> §r§7You have been given a guide book.");
        return true;
    }
}