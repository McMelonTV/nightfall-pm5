<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

class KothChestplate extends CustomItem implements Durable, Repairable {

    public function __construct(){
        $item = VanillaItems::DIAMOND_CHESTPLATE();
        $item->getNamedTag()->setInt("customitem", self::KOTHCHESTPLATE);
        $item->setCustomName("§r§aKOTH Chestplate");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7An even stronger and more durable chestplate won from KOTH.");
        ItemUtils::randomQuality($item);
        ItemUtils::maxDamage($item, 7000);
        ItemUtils::enchant($item, VanillaEnchantments::PROTECTION(), 9);

        $interface = ItemInterface::fromItem($item);

        $quality = $interface->getQuality();
        $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($quality/100)));

        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();

        parent::__construct(self::KOTHCHESTPLATE, "kothchestplate", $item);
    }
}