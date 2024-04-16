<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\Item;

class TankEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_CHESTPLATE];
    }

    public function getDescription() : string {
        return "Reduce damage received";
    }

    public function getName() : string {
        return "Tank";
    }

    public function getId() : int {
        return CustomEnchantIds::TANK;
    }

    public function getRarity() : int {
        return self::RARITY_MYTHIC;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        if(!$isAttacker){
            $dmg = $ev->getEvent()->getBaseDamage();
            $ev->getEvent()->setModifier(- ($dmg / 20) * $this->getLevel(), 14);
        }
    }
}