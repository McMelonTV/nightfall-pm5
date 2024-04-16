<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\ItemFlags;

class NightvisionEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_HELMET];
    }

    public function getDescription() : string {
        return "See in the dark";
    }

    public function getName() : string {
        return "Night Vision";
    }

    public function getId() : int {
        return CustomEnchantIds::NIGHT_VISION;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_COMMON;
    }

    public function getMaxLevel() : int {
        return 1;
    }
}