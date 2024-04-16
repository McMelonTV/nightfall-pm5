<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\ItemFlags;

class DamageEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Deals more damage when attacking";
    }

    public function getName() : string {
        return "Damage";
    }

    public function getId() : int {
        return CustomEnchantIds::DAMAGE;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::SWORD;
    }

    public function getRarity() : int {
        return self::RARITY_VERY_RARE;
    }

    public function getMaxLevel() : int {
        return 10;
    }

}