<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\VanillaItems;

class EnchantedHealApple extends CustomItem {

    public function __construct(){
        $item = VanillaItems::APPLE();
        $item->setCustomName("§r§aEnchanted Health Apple");
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::ENCHANTEDHEALAPPLE));
        $item = ItemUtils::description($item, "§r§7Get §a+4 hp §r§7when consumed");
        EnchantmentUtils::applyGlow($item);
        parent::__construct(self::ENCHANTEDHEALAPPLE, "enchantedhealapple", $item);
    }
}