<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;

class DeathbringerEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to deal more damage.";
    }

    public function getName() : string {
        return "Deathbringer";
    }

    public function getId() : int {
        return CustomEnchantIds::DEATHBRINGER;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::SWORD;
    }

    public function getRarity() : int {
        return self::RARITY_VERY_RARE;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    //events

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{

    }
}