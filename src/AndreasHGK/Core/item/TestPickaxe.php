<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\ItemInterface;
use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class TestPickaxe extends CustomItem {

    public function __construct(){
        $item = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE, 0, 1);
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