<?php

namespace AndreasHGK\Core\enchant;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\player\Player;

class CriticalEnchant extends CustomEnchant{

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to critical your opponent.";
    }

    public function getName() : string {
        return "Critical";
    }

    public function getId() : int {
        return CustomEnchantIds::CRITICAL;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::SWORD;
    }

    public function getRarity() : int {
        return self::RARITY_MYTHIC;
    }

    public function getMaxLevel() : int {
        return 3;
    }

    // events

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        if(!$isAttacker) {
            return;
        }

        $dmgEvent = $ev->getEvent();
        $damager = $dmgEvent->getDamager();
        if(!$damager instanceof Player){
            return;
        }

        if(!$damager->isSprinting() and !$damager->isFlying() and $damager->fallDistance > 0 and !$damager->getEffects()->has(VanillaEffects::BLINDNESS()) and !$damager->isUnderwater()){ // critical will already happen
            return;
        }

        if(mt_rand(1, 75) < (5 + 3 * $this->getLevel())){
            $dmgEvent->setModifier($dmgEvent->getFinalDamage() / 2, EntityDamageEvent::MODIFIER_CRITICAL);
        }
    }
}