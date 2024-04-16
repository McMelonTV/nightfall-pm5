<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\enchant\CustomEnchant;
use AndreasHGK\Core\enchant\CustomEnchantIds;
use AndreasHGK\Core\enchant\CustomEnchantsManager;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\IntUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class EnchantmentBook extends VariantItem {

    public function __construct(){
        parent::__construct(self::ENCHANTMENTBOOK, "enchantmentbook", 101);
    }

    public function getVariant(int $variant, int $level = 1): Item{
        $item = $this->getBasicItem();

        $enchant = CustomEnchantsManager::getInstance()->get($variant);
        if($enchant === null){
            $enchant = CustomEnchantsManager::getInstance()->get(CustomEnchantIds::AUTO_SMELT);
        }

        $enchant->setLevel($level);

        $item->setCustomName("§r".EnchantmentUtils::rarityColor($enchant->getRarity()).$enchant->getName().($enchant->getMaxLevel() > 1 ? " ".IntUtils::toRomanNumerals($enchant->getLevel()) : "")." §r§fBook");

        $item->getNamedTag()->setInt("nfenchantid", $enchant->getId())
                            ->setInt("nfenchantlvl", $enchant->getLevel())
                            ->setFloat("stackingbug", microtime(true));

        //$item->setNamedTag($item->getNamedTag()->setFloat("stackingbug", microtime(true)));

        ItemUtils::variant($item, $variant.":".$level);

        //$co = implode("§r§7, §b", $enchant->getCompatible());
        $rand = mt_rand(1, 32100);
        EnchantmentUtils::applyGlowLevel($item, $rand);
        $item->setLore([self::lore($enchant)]);
        //$item->setLore(["§r§7".$enchant->getDescription()."\n§r§7Rarity: §r".EnchantmentUtils::rarityColor($enchant->getRarity()).EnchantmentUtils::rarityName($enchant->getRarity())."\n§r§7Compatible: §b".$co, "§r§8Drag this book onto a compatible\n§r§8item to apply the enchantment.\n§r§8".microtime(true)]);

        return $item;
    }

    public static function lore(CustomEnchant $enchant) : string {
        $co = implode("§r§7, §b", $enchant->getCompatible());
        return "§r§7".$enchant->getDescription()."\n§r§7Rarity: §r".EnchantmentUtils::rarityColor($enchant->getRarity()).EnchantmentUtils::rarityName($enchant->getRarity())."\n§r§7Max level: §b".$enchant->getMaxLevel()."\n§r§7Compatible: §b".$co."\n§r§7Apply cost: §b{$enchant->getApplyPrice()->getXPLevels()} XP Levels\n §r§8Drag this book onto a compatible\n§r§8item to apply the enchantment.";
    }

    public function getBasicItem() : Item {
        $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
        EnchantmentUtils::applyGlow($item);
        return $item;
    }
}