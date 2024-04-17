<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\VanillaItems;

class TestPickaxe extends CustomItem {

    public function __construct(){
        $item = VanillaItems::DIAMOND_PICKAXE();
        $item->setCustomName("§r§4Test Pickaxe");
        EnchantmentUtils::applyGlow($item);

        $interface = ItemInterface::fromItem($item);
        $interface->setMaxDamage(20);
        $interface->setDamage(0);
        $interface->saveStats();
        $interface->recalculateDamage();
        $interface->recalculateLore();

        $item = $interface->getItem();
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::TESTPICKAXE));
        parent::__construct(self::TESTPICKAXE, "testpickaxe", $item);
    }
}