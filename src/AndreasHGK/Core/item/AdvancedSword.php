<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class AdvancedSword extends TieredItem implements Durable, Repairable {

    public function __construct(){
        parent::__construct(self::ADVANCEDSWORD, "advancedsword", 1);
    }

    public function getTier(int $tier): Item{
        $displayTier = $tier;
        $item = $this->getBasicItem();
        switch ($tier){
            case 2:
                ItemUtils::maxDamage($item, 2000);
                ItemUtils::variant($item, "2");
                ItemUtils::enchant($item, VanillaEnchantments::SHARPNESS(), 5);
                break;
            case 3:
                ItemUtils::maxDamage($item, 3000);
                ItemUtils::variant($item, "3");
                ItemUtils::enchant($item, VanillaEnchantments::SHARPNESS(), 6);
                break;
            case 4:
                ItemUtils::maxDamage($item, 4000);
                ItemUtils::variant($item, "4");
                ItemUtils::enchant($item, VanillaEnchantments::SHARPNESS(), 7);
                break;
            case 5:
                ItemUtils::maxDamage($item, 5000);
                ItemUtils::variant($item, "5");
                ItemUtils::enchant($item, VanillaEnchantments::SHARPNESS(), 8);
                break;
            default:
                $displayTier = 1;
                ItemUtils::maxDamage($item, 1500);
                ItemUtils::variant($item, "1");
                ItemUtils::enchant($item, VanillaEnchantments::SHARPNESS(), 4);
                break;
        }

        $item->setCustomName("§o§7T".$displayTier." ".$item->getCustomName());
        $interface = ItemInterface::fromItem($item);

        $quality = $interface->getQuality();
        $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($quality/100)));

        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::ADVANCEDSWORD));
        return $interface->getItem();
    }

    public function getBasicItem() : Item {
        $item = VanillaItems::DIAMOND_SWORD();
        $item->setCustomName("§r§cAdvanced Sword");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7A stronger and more durable sword.");
        ItemUtils::randomQuality($item);
        return $item;
    }

}