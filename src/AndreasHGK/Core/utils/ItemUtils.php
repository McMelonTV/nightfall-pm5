<?php

declare(strict_types=1);

namespace AndreasHGK\Core\utils;

use AndreasHGK\Core\enchant\CustomEnchant;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

final class ItemUtils {

    private function __construct(){
        //NOOP
    }

    public const QUALITY_TERRIBLE = -27;
    public const QUALITY_BAD = -20;
    public const QUALITY_MEDIOCRE = -10;
    public const QUALITY_AVERAGE = 0;
    public const QUALITY_STURDY = 10;
    public const QUALITY_GOOD = 20;
    public const QUALITY_LEGENDARY = 27;

    public const MIN_QUALITY = -30;
    public const MAX_QUALITY = 30;

    public static function getType(Item $item) : string {
        switch ($item->getId()){
            case ItemIds::WOODEN_PICKAXE:
            case ItemIds::STONE_PICKAXE:
            case ItemIds::IRON_PICKAXE:
            case ItemIds::GOLD_PICKAXE:
            case ItemIds::DIAMOND_PICKAXE:
                return CustomEnchant::TYPE_PICKAXE;
            case ItemIds::WOODEN_AXE:
            case ItemIds::STONE_AXE:
            case ItemIds::IRON_AXE:
            case ItemIds::GOLD_AXE:
            case ItemIds::DIAMOND_AXE:
                return CustomEnchant::TYPE_AXE;
            case ItemIds::WOODEN_SHOVEL:
            case ItemIds::STONE_SHOVEL:
            case ItemIds::IRON_SHOVEL:
            case ItemIds::GOLD_SHOVEL:
            case ItemIds::DIAMOND_SHOVEL:
                return CustomEnchant::TYPE_SHOVEL;
            case ItemIds::WOODEN_SWORD:
            case ItemIds::STONE_SWORD:
            case ItemIds::IRON_SWORD:
            case ItemIds::GOLD_SWORD:
            case ItemIds::DIAMOND_SWORD:
                return CustomEnchant::TYPE_SWORD;
            case ItemIds::LEATHER_HELMET:
            case ItemIds::CHAIN_HELMET:
            case ItemIds::IRON_HELMET:
            case ItemIds::GOLD_HELMET:
            case ItemIds::DIAMOND_HELMET:
                return CustomEnchant::TYPE_HELMET;
            case ItemIds::LEATHER_CHESTPLATE:
            case ItemIds::CHAIN_CHESTPLATE:
            case ItemIds::IRON_CHESTPLATE:
            case ItemIds::GOLD_CHESTPLATE:
            case ItemIds::DIAMOND_CHESTPLATE:
                return CustomEnchant::TYPE_CHESTPLATE;
            case ItemIds::LEATHER_LEGGINGS:
            case ItemIds::CHAIN_LEGGINGS:
            case ItemIds::IRON_LEGGINGS:
            case ItemIds::GOLD_LEGGINGS:
            case ItemIds::DIAMOND_LEGGINGS:
                return CustomEnchant::TYPE_LEGGINGS;
            case ItemIds::LEATHER_BOOTS:
            case ItemIds::CHAIN_BOOTS:
            case ItemIds::IRON_BOOTS:
            case ItemIds::GOLD_BOOTS:
            case ItemIds::DIAMOND_BOOTS:
                return CustomEnchant::TYPE_BOOTS;
            default:
                return "";
        }
    }

    public static function isCompatible(array $compatible, Item $item) : bool {
        $type = self::getType($item);
        if($type === "") {
            return false;
        }

        if(in_array($type, $compatible)) {
            return true;
        }

        switch ($type){
            case CustomEnchant::TYPE_BOOTS:
            case CustomEnchant::TYPE_LEGGINGS:
            case CustomEnchant::TYPE_CHESTPLATE:
            case CustomEnchant::TYPE_HELMET:
                if(in_array(CustomEnchant::GROUP_ARMOR, $compatible)) {
                    return true;
                }
                if(in_array(CustomEnchant::GROUP_ALL, $compatible)) {
                    return true;
                }
                break;
            case CustomEnchant::TYPE_AXE:
            case CustomEnchant::TYPE_SHOVEL:
            case CustomEnchant::TYPE_PICKAXE:
                if(in_array(CustomEnchant::GROUP_TOOLS, $compatible)) {
                    return true;
                }

                if(in_array(CustomEnchant::GROUP_ALL, $compatible)) {
                    return true;
                }

                break;
            case CustomEnchant::TYPE_SWORD:
                if(in_array(CustomEnchant::TYPE_SWORD, $compatible)) {
                    return true;
                }

                if(in_array(CustomEnchant::GROUP_ALL, $compatible)) {
                    return true;
                }

                break;
        }
        return false;
    }

    public static function sign(Item $item, string $signer) : Item {
        $lore = $item->getLore();
        array_unshift($lore, "§r§7Signed by: §b".$signer);
        $item->setLore($lore);
        return $item->setNamedTag($item->getNamedTag()->setString("signed", $signer));
    }

    public static function variant(Item $item, string $variant) : Item {
        return $item->setNamedTag($item->getNamedTag()->setString("variant", $variant));
    }

    public static function description(Item $item, string $description) : Item {
        return $item->setNamedTag($item->getNamedTag()->setString("description", $description));
    }

    public static function customID(Item $item, int $id) : Item {
        return $item->setNamedTag($item->getNamedTag()->setInt("customitem", $id));
    }

    public static function maxDamage(Item $item, int $maxDamage) : Item {
        return $item->setNamedTag(
            $item->getNamedTag()->setInt("maxDamage", $maxDamage)
                                ->setInt("nfDamage", 0)
        );
    }

    public static function enchant(Item $item, Enchantment $enchantment, int $level) : Item {
        return $item->addEnchantment(new EnchantmentInstance($enchantment, $level));
    }

    public static function randomQuality(Item $item) : Item {
        $firstRand = mt_rand(0, 100);
        if($firstRand > 95){
            $qualityRange = array_rand([self::QUALITY_TERRIBLE => self::QUALITY_TERRIBLE, self::QUALITY_LEGENDARY => self::QUALITY_LEGENDARY]);
            if($qualityRange === self::QUALITY_TERRIBLE){
                $quality = mt_rand(-30, -27);
            }else{
                $quality = mt_rand(27, 30);
            }
        }elseif($firstRand > 80){
            $qualityRange = array_rand([self::QUALITY_BAD => self::QUALITY_BAD, self::QUALITY_GOOD => self::QUALITY_GOOD]);
            if($qualityRange === self::QUALITY_BAD){
                $quality = mt_rand(-26,-20);
            }else{
                $quality = mt_rand(20, 26);
            }
        }else{
            $quality = mt_rand(-19, 19);
        }
        return $item->setNamedTag($item->getNamedTag()->setInt("quality", $quality));
    }

    public static function qualityName(int $quality) : string {
        switch (true){
            case $quality <= -27:
                $str = "§4Terrible";
                break;
            case $quality <= -20:
                $str = "§cBad";
                break;
            case $quality <= -10:
                $str = "§8Mediocre";
                break;
            case $quality < 10:
                $str = "§7Average";
                break;
            case $quality < 20:
                $str = "§eSturdy";
                break;
            case $quality < 27:
                $str = "§aGood";
                break;
            case $quality <= 30:
                $str = "§bLegendary";
                break;
        }
        return $str;
    }
}