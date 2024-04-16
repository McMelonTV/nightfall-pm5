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

class AdvancedPickaxe extends TieredItem implements Durable, Repairable {

    public function __construct(){
        parent::__construct(self::ADVANCEDPICKAXE, "advancedpickaxe", 1);
    }

    public function getTier(int $tier): Item{
        $displayTier = $tier;
        $item = $this->getBasicItem();
        switch ($tier){
            case 2:
                ItemUtils::maxDamage($item, 2000);
                ItemUtils::variant($item, "2");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 9);
                break;
            case 3:
                ItemUtils::maxDamage($item, 3000);
                ItemUtils::variant($item, "3");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 10);
                break;
            case 4:
                ItemUtils::maxDamage($item, 4000);
                ItemUtils::variant($item, "4");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 11);
                break;
            case 5:
                ItemUtils::maxDamage($item, 5000);
                ItemUtils::variant($item, "5");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 12);
                break;
            case 6:
                ItemUtils::maxDamage($item, 6000);
                ItemUtils::variant($item, "6");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 13);
                break;
            case 7:
                ItemUtils::maxDamage($item, 7000);
                ItemUtils::variant($item, "7");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 14);
                break;
            case 8:
                ItemUtils::maxDamage($item, 8000);
                ItemUtils::variant($item, "8");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 15);
                break;
            case 9:
                ItemUtils::maxDamage($item, 9000);
                ItemUtils::variant($item, "9");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 16);
                break;
            case 10:
                ItemUtils::maxDamage($item, 10000);
                ItemUtils::variant($item, "10");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 17);
                break;
            default:
                $displayTier = 1;
                ItemUtils::maxDamage($item, 1500);
                ItemUtils::variant($item, "1");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 8);
                break;
        }

        $item->setCustomName("§o§7T".$displayTier." ".$item->getCustomName());
        $interface = ItemInterface::fromItem($item);

        $quality = $interface->getQuality();
        $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($quality/100)));

        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::ADVANCEDPICKAXE));
        return $interface->getItem();
    }

    public function getBasicItem() : Item {
        $item = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE, 0, 1);
        $item->setCustomName("§r§cAdvanced Pickaxe");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7The advanced pickaxe is faster and more durable than the basic variant.");
        ItemUtils::randomQuality($item);
        return $item;
    }
}