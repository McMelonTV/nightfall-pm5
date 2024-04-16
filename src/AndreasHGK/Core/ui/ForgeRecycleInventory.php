<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\ItemInterface;
use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use pocketmine\player\Player;

class ForgeRecycleInventory {

    public static function sendTo(Player $sender) : void {
        $menu = InvMenu::create(InvMenu::TYPE_HOPPER);

        $menu->setName("§l§7Recycle inventory");
        $menu->setListener($menu->readonly());

        $menu->setInventoryCloseListener(static function(Player $player, InvMenuInventory $inventory) use ($menu){
            $count = 0;
            foreach($inventory->getContents(false) as $content){
                $interface = ItemInterface::fromItem($content);
                if(!$interface->isCustomItem()){
                    $player->getInventory()->addItem($content);
                    continue;
                }

                switch ($interface->getCustomItem()->getId()){
                    case 6:
                        break;
                }

                ++$count;
            }

            $player->sendMessage("§r§b§l> §r§7You recycled§b $count §r§7items.");
        });

        $menu->send($sender);
    }
}