<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\Price;
use AndreasHGK\Core\user\UserManager;
use AndreasHGK\Core\utils\ItemUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ReforgeForm {

    public static function sendTo(Player $sender) : void {
        if($sender->getInventory()->getItemInHand()->getId() === ItemIds::AIR){
            $sender->sendMessage("§r§c§l> §r§7Please hold an item to reforge.");
            return;
        }

        $hand = $sender->getInventory()->getItemInHand();
        $interface = ItemInterface::fromItem($hand);

        if($interface->getQuality() <= -99){
            $sender->sendMessage("§r§c§l> §r§7You can't reforge this item.");
            return;
        }

        $ui = new SimpleForm(static function (Player $sender, ?string $data){
            if($data === null){
                return;
            }

            if($data !== "confirm"){
                return;
            }

            if($sender->getInventory()->getItemInHand()->getId() === ItemIds::AIR){
                $sender->sendMessage("§r§c§l> §r§7Please hold an item to rename.");
                return;
            }

            $hand = $sender->getInventory()->getItemInHand();

            $price = new Price();

            if(!$price->canAfford($sender)){
                $sender->sendMessage("§r§c§l> §r§7You can't afford to disenchant this.");
                return;
            }

            $user = UserManager::getInstance()->getOnline($sender);
            if($user === null) {
                return;

            }

            $interface = ItemInterface::fromItem($hand);

            if($interface->getQuality() <= -99){
                $sender->sendMessage("§r§c§l> §r§7You can't reforge this item.");
                return;
            }

            $hand = ItemUtils::randomQuality($hand);

            $interface = ItemInterface::fromItem($hand);

            //todo: make it actually work
            $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($interface->getQuality()/100)));

            $interface->recalculateDamage();
            $interface->recalculateLore();
            $interface->saveStats();

            $sender->getInventory()->setItemInHand($interface->getItem());

            $price->pay($sender);
            $sender->sendMessage("§r§b§l> §r§7You successfully reforged your item for §b0 stardust §r§7and §b0 levels§r§7.");
        });
        $ui->setTitle("§bItem reforging");
        $str = "§r§7This will change the quality of the item in your hand to a random quality.";
        $str .= "§r§7For the item you are holding, it will cost §b0 stardust §r§7and §b0 levels§r§7.";
        $ui->setContent($str);
        $ui->addButton("§8Confirm reforging", -1, "", "confirm");
        $ui->addButton("§8Cancel", -1, "", "cancel");

        $sender->sendForm($ui);
    }
}