<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\Price;
use AndreasHGK\Core\utils\ItemUtils;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RenameForm{

    public static function sendTo(Player $sender) : void {
        $hand = $sender->getInventory()->getItemInHand();
        if($hand->getId() === ItemIds::AIR){
            $sender->sendMessage("§r§c§l> §r§7Please hold an item to rename.");
            return;
        }

        $ui = new CustomForm(static function (Player $sender, ?array $data) use($hand){
            if($data === null){
                return;
            }

            if(!isset($data["name"])){
                $sender->sendMessage('§r§c§l> §r§7Please enter a name for your item.');
                return;
            }

            if($sender->getInventory()->getItemInHand()->equalsExact($hand) === false){
                $sender->sendMessage("§r§c§l> §r§7Please hold an item to rename.");
                return;
            }

            $name = $data["name"];
            if($name === "" || strlen($name) < 3){
                $sender->sendMessage("§r§c§l> §r§7The item name has to be longer than 3 characters.");
                return;
            }

            if(TextFormat::clean(TextFormat::colorize($name)) === ""){
                $sender->sendMessage("§r§c§l> §r§7The item name must be visible.");
                return;
            }

            $price = new Price(0, 0, 0, 0, 0, 0, 25);
            if(!$price->canAfford($sender)){
                $sender->sendMessage("§r§c§l> §r§7You need at least 25 levels to rename an item.");
                return;
            }

            switch ($hand->getId()){
                case ItemIds::ENCHANTED_BOOK:
                case ItemIds::GLOWSTONE:
                case ItemIds::GLOWSTONE_DUST:
                case ItemIds::DYE:
                case ItemIds::TRIPWIRE_HOOK:
                    $sender->sendMessage("§r§c§l> §r§7You can't rename this item.");
                    return;
            }

            $hand->setCustomName(TextFormat::colorize($name));
            ItemUtils::sign($hand, $sender->getName());
            $sender->getInventory()->setItemInHand($hand);
            $price->pay($sender);
            $sender->sendMessage("§r§c§l> §r§7You renamed the item in your hand to §r".$name."§r§7 for §b25 levels§r§7.");
        });
        $ui->setTitle("§bRename item");
        $ui->addLabel("§r§7Please select a name to give the item you are holding. Renaming any item will cost 25 XP levels.");
        $ui->addInput("§b§l> §r§7Item name", "", $hand->getCustomName() ?? "name", "name");

        $sender->sendForm($ui);
    }
}