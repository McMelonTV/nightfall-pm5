<?php

namespace AndreasHGK\Core\ui;

use muqsit\invmenu\InvMenu;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class InventoryseeInventory {

    public static function sendTo(Player $player, string $name, array $inventoryItems, array $armorItems) : void{
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);

        $menu->setName("§l§o§b".$name."§r§8's inventory");
        $menu->setListener($menu->readonly());

        $key = 0;
        foreach ($inventoryItems as $item){
            if($key < 9){
                $menu->getInventory()->setItem($key++ + 27, clone $item);
            }elseif($key >= 9){
                $menu->getInventory()->setItem($key++ - 9, clone $item);
            }
        }

        $pane = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED());
        for($i = 36; $i < 45; ++$i){
            $menu->getInventory()->setItem($i, clone $pane->asItem());
        }

        $key = 45;
        foreach($armorItems as $armor){
            $menu->getInventory()->setItem($key++, clone $armor);
        }

        $menu->send($player);
    }
}