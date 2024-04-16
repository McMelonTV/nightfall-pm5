<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\item\CustomItem;
use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\EnchantmentBook;
use AndreasHGK\Core\Price;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\IntUtils;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class EnchantmentForgeForm {

    public static function sendTo(Player $sender) : void {
        $ui = new SimpleForm(static function (Player $player, ?string $data) {
            if($data === null){
                return;
            }

            switch ($data){
                case "normal":
                    $price = new Price(0, 0, 25);
                    $high = false;
                    break;
                case "high-end":
                    $price = new Price(0, 0, 40);
                    $high = true;
                    break;
                default:
                    $player->sendMessage("§r§c§l> §r§7That is not a valid forge option.");
                    return;
            }

            if(!$price->canAfford($player)){
                $player->sendMessage("§r§c§l> §r§7You don't have enough magic dust to forge this enchantment.");
                return;
            }

            $enchant = CustomEnchantsManager::getInstance()->getRandomEnchantment($high);
            if($high){
                $enchant2 = CustomEnchantsManager::getInstance()->getRandomEnchantment(true);
                if($enchant2 === null){
                    $player->sendMessage("§r§c§l> §r§7Could not find a compatible enchantment.");
                    return;
                }

                if($enchant2->getRarity() < $enchant->getRarity()){
                    $enchant = $enchant2;
                }elseif($enchant2->getRarity() === $enchant->getRarity()){
                    $enchant->setLevel(min(2, $enchant->getMaxLevel()));
                }
            }

            if($enchant === null){
                $player->sendMessage("§r§c§l> §r§7Could not find a compatible enchantment.");
                return;
            }

            $bookClass = CustomItemManager::getInstance()->get(CustomItem::ENCHANTMENTBOOK);
            if(!$bookClass instanceof EnchantmentBook){
                return;
            }

            $item = $bookClass->getVariant($enchant->getId(), $enchant->getLevel());
            if(!$player->getInventory()->canAddItem($item)){
                $player->sendMessage("§c§l> §r§7You don't have enough space in your inventory to forge this enchantment.");
                return;
            }

            $player->getInventory()->addItem($item);
            $price->pay($player);
            $player->sendMessage("§b§l> §r§7You have forged §r".EnchantmentUtils::rarityColor($enchant->getRarity()).$enchant->getName().($enchant->getMaxLevel() <= 1 ? "" : " ".IntUtils::toRomanNumerals($enchant->getLevel()))."§r§7.");
        });
        $ui->setTitle("§bEnchantment Forge");

        $ui->setContent("§r§7Here you can spend magic dust to forge a random enchantment. If you forge a high-end enchantment you will have a higher chance to get a better enchantment. You're still able to get all enchants from both types.");

        $ui->addButton("§r§9Normal §r§8enchantment"."\n§r§125 Magic dust", -1, "", "normal");
        $ui->addButton("§r§9High-end §r§8enchantment"."\n§r§140 Magic dust", -1, "", "high-end");

        $sender->sendForm($ui);
    }
}