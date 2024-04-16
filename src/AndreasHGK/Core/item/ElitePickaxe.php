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

class ElitePickaxe extends TieredItem implements Durable, Repairable {

    public function __construct(){
        parent::__construct(self::ELITEPICKAXE, "elitepickaxe", 1);
    }

    public function getTier(int $tier): Item{
        $displayTier = $tier;
        $item = $this->getBasicItem();
        switch ($tier){
            case 2:
                ItemUtils::maxDamage($item, 20000);
                ItemUtils::variant($item, "2");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 55);
                break;
            case 3:
                ItemUtils::maxDamage($item, 25000);
                ItemUtils::variant($item, "3");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 65);
                break;
            default:
                $displayTier = 1;
                ItemUtils::maxDamage($item, 15000);
                ItemUtils::variant($item, "1");
                ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 45);
                break;
        }

        $item->setCustomName("§o§7T".$displayTier." ".$item->getCustomName());
        $interface = ItemInterface::fromItem($item);

        $quality = $interface->getQuality();
        $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($quality/100)));

        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::ELITEPICKAXE));
        return $interface->getItem();
    }

    public function getBasicItem() : Item {
        $item = ItemFactory::getInstance()->get(ItemIds::GOLD_PICKAXE, 0, 1);
        $item->setCustomName("§r§9Elite Pickaxe");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7This pickaxe mines faster than any other!");
        ItemUtils::randomQuality($item);
        return $item;
    }

}