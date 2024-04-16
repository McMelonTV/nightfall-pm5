<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\player\Player;

class FreezeEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to slow a player down on hit for 3 seconds.";
    }

    public function getName() : string {
        return "Freeze";
    }

    public function getId() : int {
        return CustomEnchantIds::FREEZE;
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
        $chance = 5;
        //$chance += 2*min($this->level, 5);
        $chance += 2*($this->level-1);
        if(mt_rand(0, 100) < $chance){
            $player = $ev->getEvent()->getEntity();
            if(!$player instanceof Player) {
                return;
            }

            $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 60, 1));
        }
    }
}