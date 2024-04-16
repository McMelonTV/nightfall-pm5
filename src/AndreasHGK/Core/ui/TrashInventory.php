<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use pocketmine\player\Player;

class TrashInventory {

    public static function sendTo(Player $sender) : void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);

        $menu->setName("§l§7Trash bin");

        $menu->setInventoryCloseListener(static function(Player $player, InvMenuInventory $inventory) use ($menu){
            $count = 0;
            foreach($inventory->getContents(false) as $content){
                $count += $content->getCount();
            }
            if($count === 0) return;
            $player->sendMessage("§r§b§l> §r§7You removed§b $count §r§7items.");
        });

        $menu->send($sender);
    }
}