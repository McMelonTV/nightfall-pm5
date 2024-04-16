<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\player\Player;

class FireAspectEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to light a player on fire.";
    }

    public function getName() : string {
        return "Fire Aspect";
    }

    public function getId() : int {
        return CustomEnchantIds::FIRE_ASPECT;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::SWORD;
    }

    public function getRarity() : int {
        return self::RARITY_UNCOMMON;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    //events

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        $chance = 13;
        //$chance += 2*min($this->level, 5);
        $chance += 7*($this->level-1);
        if(mt_rand(0, 100) < $chance){
            $player = $ev->getEvent()->getEntity();
            if(!$player instanceof Player) {
                return;
            }

            $player->setOnFire(5);
        }
    }
}