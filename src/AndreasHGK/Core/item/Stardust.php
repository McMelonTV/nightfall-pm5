<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class Stardust extends CustomItem implements RepairResource {

    public function getRepairValue(): int{
        return 200;
    }

    public function __construct(){
        $item = ItemFactory::getInstance()->get(ItemIds::GLOWSTONE_DUST, 0, 1);
        $item->setCustomName("§r§eStardust");
        EnchantmentUtils::applyGlow($item);
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::STARDUST));
        $item->setLore(["§r§7Can be used to forge advanced tools and armor."]);
        parent::__construct(self::STARDUST, "stardust", $item);
    }
}