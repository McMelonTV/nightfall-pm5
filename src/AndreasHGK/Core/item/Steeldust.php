<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\VanillaItems;

class Steeldust extends CustomItem implements RepairResource {

    public function getRepairValue(): int{
        return 50;
    }

    public function __construct(){
        $item = VanillaItems::SUGAR();
        $item->setCustomName("§r§fSteeldust");
        EnchantmentUtils::applyGlow($item);
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::STEELDUST));
        $item->setLore(["§r§7Can be used to forge basic tools and armor."]);
        parent::__construct(self::STEELDUST, "steeldust", $item);
    }
}