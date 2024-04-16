<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class EnchantedHealApple extends CustomItem {

    public function __construct(){
        $item = ItemFactory::getInstance()->get(ItemIds::APPLE, 18, 1);
        $item->setCustomName("§r§aEnchanted Health Apple");
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::ENCHANTEDHEALAPPLE));
        $item = ItemUtils::description($item, "§r§7Get §a+4 hp §r§7when consumed");
        EnchantmentUtils::applyGlow($item);
        parent::__construct(self::ENCHANTEDHEALAPPLE, "enchantedhealapple", $item);
    }
}