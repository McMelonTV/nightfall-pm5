<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

class KothPickaxe extends CustomItem implements Durable, Repairable {

    public function __construct(){
        $item = VanillaItems::DIAMOND_PICKAXE();
        $item->setCustomName("§r§aKOTH Pickaxe");
        EnchantmentUtils::applyGlow($item);
        ItemUtils::description($item, "§r§7The KOTH pickaxe is even faster and more durable than an advanced pickaxe.");
        ItemUtils::randomQuality($item);
        ItemUtils::maxDamage($item, 11000);
        ItemUtils::enchant($item, VanillaEnchantments::EFFICIENCY(), 18);

        $interface = ItemInterface::fromItem($item);

        $quality = $interface->getQuality();
        $interface->setMaxDamage($interface->getMaxDamage()+(int)($interface->getMaxDamage()*($quality/100)));

        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();
        $item->getNamedTag()->setInt("customitem", self::KOTHPICKAXE);
        parent::__construct(self::KOTHPICKAXE, "kothpickaxe", $item);
    }
}