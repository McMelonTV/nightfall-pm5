<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class BasicPickaxe extends TieredItem implements Durable, Repairable {

    public function __construct(){
        parent::__construct(self::BASICPICKAXE, "basicpickaxe", 1);
    }

    public function getTier(int $tier): Item{
        $displayTier = $tier;
        $item = $this->getBasicItem();
        switch ($tier){
            case 2:
                ItemUtils::maxDamage($item, 400);
                ItemUtils::variant($item, "2");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 1);
                break;
            case 3:
                ItemUtils::maxDamage($item, 600);
                ItemUtils::variant($item, "3");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 2);
                break;
            case 4:
                ItemUtils::maxDamage($item, 800);
                ItemUtils::variant($item, "4");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 3);
                break;
            case 5:
                ItemUtils::maxDamage($item, 1000);
                ItemUtils::variant($item, "5");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 4);
                break;
            case 6:
                ItemUtils::maxDamage($item, 1200);
                ItemUtils::variant($item, "6");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 5);
                break;
            case 7:
                ItemUtils::maxDamage($item, 1400);
                ItemUtils::variant($item, "7");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 6);
                break;
            case 8:
                ItemUtils::maxDamage($item, 1600);
                ItemUtils::variant($item, "8");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 7);
                break;
            case 9:
                ItemUtils::maxDamage($item, 1800);
                ItemUtils::variant($item, "9");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 8);
                break;
            case 10:
                ItemUtils::maxDamage($item, 2000);
                ItemUtils::variant($item, "10");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 9);
                break;
            default:
                $displayTier = 1;
                ItemUtils::variant($item, "1");
                ItemUtils::maxDamage($item, 200);
                break;
        }
        $item->setCustomName("§o§7T".$displayTier." ".$item->getCustomName());
        $interface = ItemInterface::fromItem($item);

        $quality = $interface->getQuality();
        $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($quality/100)));

        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::BASICPICKAXE));
        return $interface->getItem();
    }

    public function getBasicItem() : Item {
        $item = ItemFactory::getInstance()->get(ItemIds::IRON_PICKAXE, 0, 1);
        $item->setCustomName("§r§bBasic Pickaxe");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7A basic pickaxe, crafted using steeldust.");
        ItemUtils::randomQuality($item);
        return $item;
    }
}