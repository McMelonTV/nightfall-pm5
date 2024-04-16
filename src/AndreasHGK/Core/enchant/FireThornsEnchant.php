<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\player\Player;

class FireThornsEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_CHESTPLATE];
    }

    public function getDescription() : string {
        return "Has a chance to light an enemy on fire when they hit you.";
    }

    public function getName() : string {
        return "Fire Thorns";
    }

    public function getId() : int {
        return CustomEnchantIds::FIRE_THORNS;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_UNCOMMON;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    //events

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        $chance = 8;
        //$chance += 2*min($this->level, 5);
        $chance += 3*($this->level-1);
        if(mt_rand(0, 100) < $chance){
            $player = $ev->getEvent()->getDamager();
            if(!$player instanceof Player) {
                return;
            }

            $player->setOnFire(4);
        }
    }
}