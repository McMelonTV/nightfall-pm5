<?php

declare(strict_types=1);

namespace AndreasHGK\Core\command;

use AndreasHGK\Core\Core;
use AndreasHGK\Core\item\Durable;
use AndreasHGK\Core\ItemInterface;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FixCommand extends Executor{

    public function __construct(){
        parent::__construct("fix", "repair an item", "/fix", Core::PERM_MAIN."command.fix", ["repair"]);
        $this->addParameterMap(0);
        $this->addSingleParameter(0, 0, " ", " ", " ", false, true);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player) {
            return false;
        }

        $item = $sender->getInventory()->getItemInHand();
        $interface = ItemInterface::fromItem($item);

        if(!$interface->getCustomItem() instanceof Durable){
            $sender->sendMessage("§r§b§l>§r§7 You can't repair this item.");
            return true;
        }

        $interface->setDamage(0);

        $interface->recalculateDamage();
        $interface->recalculateLore();
        $interface->saveStats();

        $sender->getInventory()->setItemInHand($item);

        $sender->sendMessage("§r§b§l>§r§7 You have repaired the item in your hand.");
        return true;
    }

}