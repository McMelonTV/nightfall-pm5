<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\player\Player;

class IceThornsEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_CHESTPLATE];
    }

    public function getDescription() : string {
        return "Has a chance to slow a player down for 3 seconds when they hit you.";
    }

    public function getName() : string {
        return "Ice Thorns";
    }

    public function getId() : int {
        return CustomEnchantIds::ICE_THORNS;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::ARMOR;
    }

    public function getRarity() : int {
        return self::RARITY_RARE;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    //events

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        $chance = 4;
        //$chance += 2*min($this->level, 5);
        $chance += 1*($this->level-1);
        if(mt_rand(0, 100) < $chance){
            $player = $ev->getEvent()->getDamager();
            if(!$player instanceof Player) {
                return;
            }

            $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 60, 1));
        }
    }
}