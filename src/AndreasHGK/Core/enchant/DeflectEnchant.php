<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\Item;

class DeflectEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to deflect someones hit on themself";
    }

    public function getName() : string {
        return "Deflect";
    }

    public function getId() : int {
        return CustomEnchantIds::DEFLECT;
    }

    public function getRarity() : int {
        return self::RARITY_UNCOMMON;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        if(!$isAttacker && mt_rand(0, 100) < 2*$this->getLevel()){
            $ev->setDeflect(true);
        }
    }
}