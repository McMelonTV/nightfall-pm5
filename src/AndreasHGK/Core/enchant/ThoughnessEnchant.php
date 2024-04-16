<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\Item;

class ThoughnessEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_CHESTPLATE];
    }

    public function getDescription() : string {
        return "Reduce damage received from swords";
    }

    public function getName() : string {
        return "Toughness";
    }

    public function getId() : int {
        return CustomEnchantIds::TOUGHNESS;
    }

    public function getRarity() : int {
        return self::RARITY_VERY_RARE;
    }

    public function getMaxLevel() : int {
        return 10;
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        $ev->setToughness($ev->getToughness() + $this->getLevel());
    }
}