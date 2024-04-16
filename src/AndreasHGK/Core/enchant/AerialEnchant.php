<?php

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class AerialEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Deal more damage to opponents in air.";
    }

    public function getName() : string {
        return "Aerial";
    }

    public function getId() : int {
        return CustomEnchantIds::AERIAL;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::SWORD;
    }

    public function getRarity() : int {
        return self::RARITY_VERY_RARE;
    }

    public function getMaxLevel() : int {
        return 6;
    }
}