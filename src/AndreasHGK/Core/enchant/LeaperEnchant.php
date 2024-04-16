<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class LeaperEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_BOOTS];
    }

    public function getDescription() : string {
        return "Get jump boost";
    }

    public function getName() : string {
        return "Leaper";
    }

    public function getId() : int {
        return CustomEnchantIds::LEAPER;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_COMMON;
    }

    public function getMaxLevel() : int {
        return 2;
    }
}