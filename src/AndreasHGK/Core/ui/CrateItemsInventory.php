<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\crate\Crate;
use muqsit\invmenu\InvMenu;
use pocketmine\player\Player;

class CrateItemsInventory {

    public static function sendTo(Player $sender, Crate $crate) : void {
        $itemCount = count($crate->getItems());

        if($itemCount <= 5){
            $menu = InvMenu::create(InvMenu::TYPE_HOPPER);
        }elseif($itemCount <= 27){
            $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        }else{
            $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        }

        $menu->setName("§l§o§b".$crate->getName()."§r§8 crate items");
        $menu->setListener($menu->readonly());

        /*$menu->setListener(function (Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action) use ($menu){
            return false;
        });*/
        /*$menu->setInventoryCloseListener(function(Player $player, InvMenuInventory $inventory) use ($menu){

        });*/
        $total = $crate->getTotalChance();

        $key = 0;
        foreach ($crate->getItems() as $crateItem){
            $item = clone $crateItem->getItem();
            $lore = $item->getLore();
            $lore[] = "§r§7Drop chance: §b".round($crateItem->getChance()/$total*100, 2)."%";
            $item->setLore($lore);
            $menu->getInventory()->setItem($key, $item);
            ++$key;
        }

        $menu->send($sender);
    }
}