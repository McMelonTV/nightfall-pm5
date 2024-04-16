<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class HealthEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::GROUP_ARMOR];
    }

    public function getDescription() : string {
        return "Get an extra heart of HP.";
    }

    public function getName() : string {
        return "Health";
    }

    public function getId() : int {
        return CustomEnchantIds::HEALTH;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_LEGENDARY;
    }

    public function getMaxLevel() : int {
        return 1;
    }
}