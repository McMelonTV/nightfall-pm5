<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class RunnerEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_BOOTS];
    }

    public function getDescription() : string {
        return "Get a speed boost";
    }

    public function getName() : string {
        return "Runner";
    }

    public function getId() : int {
        return CustomEnchantIds::RUNNER;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_UNCOMMON;
    }

    public function getMaxLevel() : int {
        return 2;
    }
}