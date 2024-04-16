<?php

declare(strict_types=1);

namespace AndreasHGK\Core\ui;

use AndreasHGK\Core\enchant\CustomEnchant;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\utils\EnchantmentUtils;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class EnchantmentlistForm {

    public static function sendTo(Player $sender) : void {
        $enchants = [];
        foreach(CustomEnchantsManager::getInstance()->getAll() as $enchant) {
            $enchants[$enchant->getRarity()][] = $enchant;
        }

        $ui = new SimpleForm(static function (Player $player, $data) use ($enchants) {
            if($data === null) {
                return;
            }

            $ui2 = new CustomForm(null);
            if(!isset($enchants[(int) $data])){
                return;
            }

            $str = "§r§fHere is a list of the available " . EnchantmentUtils::rarityName((int) $data) . " enchantments currently in the server.";
            /** @var $enchants CustomEnchant[][] */
            foreach($enchants[(int) $data] as $enchant){
                $str .= "§r\n\n".EnchantmentUtils::rarityColor($enchant->getRarity()).$enchant->getName();
                $str .= "\n§r§7Description: §b".$enchant->getDescription();
                $str .= "\n§r§7Max level: §b".$enchant->getMaxLevel();
                $str .= "\n§r§7Compatible: §b".implode("§r§7, §b", $enchant->getCompatible());;
            }

            $ui2->setTitle(EnchantmentUtils::rarityName((int) $data) . "enchants");
            $ui2->addLabel($str);

            $player->sendForm($ui2);
        });
        $ui->setTitle("§bEnchantment list");

        $ui->addButton(self::completeName(CustomEnchant::RARITY_COMMON), -1, "", (string) CustomEnchant::RARITY_COMMON);
        $ui->addButton(self::completeName(CustomEnchant::RARITY_UNCOMMON), -1, "", (string) CustomEnchant::RARITY_UNCOMMON);
        $ui->addButton(self::completeName(CustomEnchant::RARITY_RARE), -1, "", (string) CustomEnchant::RARITY_RARE);
        $ui->addButton(self::completeName(CustomEnchant::RARITY_VERY_RARE), -1, "", (string) CustomEnchant::RARITY_VERY_RARE);
        $ui->addButton(self::completeName(CustomEnchant::RARITY_MYTHIC), -1, "", (string) CustomEnchant::RARITY_MYTHIC);
        $ui->addButton(self::completeName(CustomEnchant::RARITY_LEGENDARY), -1, "", (string) CustomEnchant::RARITY_LEGENDARY);

        $sender->sendForm($ui);
    }

    private static function completeName(int $rarity) : string{
        return EnchantmentUtils::rarityColor($rarity) . EnchantmentUtils::rarityName($rarity);
    }
}