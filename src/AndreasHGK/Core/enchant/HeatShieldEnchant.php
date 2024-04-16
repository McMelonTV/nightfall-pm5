<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class HeatShieldEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_CHESTPLATE];
    }

    public function getDescription() : string {
        return "Reduces fire damage.";
    }

    public function getName() : string {
        return "Heatshield";
    }

    public function getId() : int {
        return CustomEnchantIds::HEAT_SHIELD;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_RARE;
    }

    public function getMaxLevel() : int {
        return 4;
    }

    //events

    public function onGetDamage(EntityDamageEvent $ev): void{
        if($ev->getCause() !== EntityDamageEvent::CAUSE_FIRE || $ev->getCause() !== EntityDamageEvent::CAUSE_FIRE_TICK) {
            return;
        }

        $ev->setBaseDamage($ev->getBaseDamage() - ($ev->getBaseDamage()/10)*($this->getLevel()+1));
    }
}