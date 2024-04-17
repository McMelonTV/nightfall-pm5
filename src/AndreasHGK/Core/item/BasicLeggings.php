<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class BasicLeggings extends TieredItem implements Durable, Repairable {

    public function __construct(){
        parent::__construct(self::BASICLEGGINGS, "basicleggings", 1);
    }

    public function getTier(int $tier): Item{
        $displayTier = $tier;
        $item = $this->getBasicItem();
        switch ($tier){
            case 2:
                ItemUtils::maxDamage($item, 400);
                ItemUtils::variant($item, "2");
                ItemUtils::enchant($item, VanillaEnchantments::PROTECTION(), 1);
                break;
            case 3:
                ItemUtils::maxDamage($item, 600);
                ItemUtils::variant($item, "3");
                ItemUtils::enchant($item, VanillaEnchantments::PROTECTION(), 2);
                break;
            case 4:
                ItemUtils::maxDamage($item, 800);
                ItemUtils::variant($item, "4");
                ItemUtils::enchant($item, VanillaEnchantments::PROTECTION(), 3);
                break;
            case 5:
                ItemUtils::maxDamage($item, 1000);
                ItemUtils::variant($item, "5");
                ItemUtils::enchant($item, VanillaEnchantments::PROTECTION(), 4);
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
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::BASICLEGGINGS));
        return $interface->getItem();
    }

    public function getBasicItem() : Item {
        $item = VanillaItems::IRON_LEGGINGS();
        $item->setCustomName("§r§bBasic Leggings");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7Basic leggings, crafted using steeldust.");
        ItemUtils::randomQuality($item);
        return $item;
    }
}