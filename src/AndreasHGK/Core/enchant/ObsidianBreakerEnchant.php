<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

class ObsidianBreakerEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_PICKAXE];
    }

    public function getDescription() : string {
        return "Chance to instantly break obsidian";
    }

    public function getName() : string {
        return "Obsidian Breaker";
    }

    public function getId() : int {
        return CustomEnchantIds::OBSIDIAN_BREAKER;
    }

    public function getRarity() : int {
        return self::RARITY_LEGENDARY;
    }

    public function getMaxLevel() : int {
        return 5;
    }

}