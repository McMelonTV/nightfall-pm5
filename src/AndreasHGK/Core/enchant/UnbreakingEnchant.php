<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class UnbreakingEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::GROUP_ALL];
    }

    public function getDescription() : string {
        return "Has a chance to not take away durability on item use.";
    }

    public function getName() : string {
        return "Unbreaking";
    }

    public function getId() : int {
        return CustomEnchantIds::UNBREAKING;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ALL;
    }

    public function getRarity() : int {
        return self::RARITY_RARE;
    }

    public function getMaxLevel() : int {
        return 6;
    }
}