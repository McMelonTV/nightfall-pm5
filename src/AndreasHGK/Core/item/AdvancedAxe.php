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

class AdvancedAxe extends TieredItem implements Durable, Repairable {

    public function __construct(){
        parent::__construct(self::ADVANCEDAXE, "advancedaxe", 1);
    }

    public function getTier(int $tier): Item{
        $displayTier = $tier;
        $item = $this->getBasicItem();
        switch ($tier){
            case 2:
                ItemUtils::maxDamage($item, 5000);
                ItemUtils::variant($item, "2");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 12);
                break;
            case 3:
                ItemUtils::maxDamage($item, 10000);
                ItemUtils::variant($item, "3");
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

        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::ADVANCEDAXE));
        return $interface->getItem();
    }

    public function getBasicItem() : Item {
        $item = ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE, 0, 1);

        $item->setCustomName("§r§cAdvanced Axe");

        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7The advanced axe is faster and more durable than the basic variant.");
        ItemUtils::randomQuality($item);
        return $item;
    }

}