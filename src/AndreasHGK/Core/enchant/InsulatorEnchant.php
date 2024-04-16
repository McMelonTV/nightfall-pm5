<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class InsulatorEnchant extends CustomEnchant{

    public function getCompatible() : array {
        return [self::TYPE_HELMET];
    }

    public function getDescription() : string {
        return "Insulates lightning.";
    }

    public function getName() : string {
        return "Insulator";
    }

    public function getId() : int {
        return CustomEnchantIds::INSULATOR;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::HEAD;
    }

    public function getRarity() : int {
        return self::RARITY_RARE;
    }

    public function getMaxLevel() : int {
        return 3;
    }
}