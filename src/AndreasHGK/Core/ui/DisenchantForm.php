<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\enchant\CustomEnchant;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\EnchantmentBook;
use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\Price;
use AndreasHGK\Core\user\UserManager;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class DisenchantForm {

    public static function sendTo(Player $sender) : void {
        if($sender->getInventory()->getItemInHand()->getId() === ItemIds::AIR){
            $sender->sendMessage("§r§c§l> §r§7Please hold an item to disenchant.");
            return;
        }

        $hand = $sender->getInventory()->getItemInHand();
        $interface = ItemInterface::fromItem($hand);

        if(empty($interface->getCustomEnchants())){
            $sender->sendMessage("§r§c§l> §r§7Please hold an item with enchantments.");
            return;
        }

        $magicdust = 0;
        $xplevel = 0;
        foreach($interface->getCustomEnchants() as $enchant){
            switch ($enchant->getRarity()){
                case CustomEnchant::RARITY_COMMON:
                    $magicdust += 5*$enchant->getLevel();
                    $xplevel += 1*$enchant->getLevel();
                    break;
                case CustomEnchant::RARITY_UNCOMMON:
                    $magicdust += 8*$enchant->getLevel();
                    $xplevel += 3*$enchant->getLevel();
                    break;
                case CustomEnchant::RARITY_RARE:
                    $magicdust += 14*$enchant->getLevel();
                    $xplevel += 7*$enchant->getLevel();
                    break;
                case CustomEnchant::RARITY_VERY_RARE:
                    $magicdust += 17*$enchant->getLevel();
                    $xplevel += 10*$enchant->getLevel();
                    break;
                case CustomEnchant::RARITY_MYTHIC:
                    $magicdust += 22*$enchant->getLevel();
                    $xplevel += 15*$enchant->getLevel();
                    break;
                case CustomEnchant::RARITY_LEGENDARY:
                    $magicdust += 25*$enchant->getLevel();
                    $xplevel += 18*$enchant->getLevel();
                    break;
                default:
                    break;
            }
        }

        $ui = new SimpleForm(static function (Player $sender, ?string $data) use ($hand, $interface, $magicdust, $xplevel) {
            if($data === null){
                return;
            }

            if($data !== "confirm"){
                return;
            }

            if($sender->getInventory()->getItemInHand()->getId() === ItemIds::AIR){
                $sender->sendMessage("§r§c§l> §r§7Please hold an item to disenchant.");
                return;
            }

            $price = new Price();
            $price->setMagicdust($magicdust);
            $price->setXPLevels($xplevel);
            if(!$price->canAfford($sender)){
                $sender->sendMessage("§r§c§l> §r§7You can't afford to disenchant this.");
                return;
            }

            $enchants = $interface->getCustomEnchants();
            $books = [];
            /** @var EnchantmentBook $bookClass */
            $bookClass = CustomItemManager::getInstance()->get(CustomItem::ENCHANTMENTBOOK);
            foreach($enchants as $enchant){
                $book = $bookClass->getVariant($enchant->getId(), $enchant->getLevel());
                $books[] = $book;
            }

            $interface->setCustomEnchants([]);
            $interface->saveStats();
            $interface->recalculateLore();
            $hand = $interface->getItem();

            $user = UserManager::getInstance()->getOnline($sender);
            if($user === null) {
                return;
            }

            $user->safeGiveMultiple($books);
            $sender->getInventory()->setItemInHand($hand);

            $price->pay($sender);
            $sender->sendMessage("§r§c§l> §r§7You successfully disenchanted your item for§b $magicdust magicdust§r§7 and§b $xplevel levels§r§7.");
        });
        $ui->setTitle("§bItem disenchantment");
        $str = "§r§7This will remove all of the enchantments of the item in your hand and give the enchantments to you. Doing this will come at a cost based on what enchantments you have.";
        $str .= "§r§7For the item you are holding, it will cost §b".$magicdust." magicdust §r§7and §b".$xplevel." levels§r§7.";
        $ui->setContent($str);
        $ui->addButton("§8Confirm disenchanting", -1, "", "confirm");
        $ui->addButton("§8Cancel", -1, "", "cancel");

        $sender->sendForm($ui);
    }
}