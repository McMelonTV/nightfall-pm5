<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\item\Item;

class PoisonEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Has a chance to give poison for 5 seconds to someone when attacking";
    }

    public function getName() : string {
        return "Poison";
    }

    public function getId() : int {
        return CustomEnchantIds::POISON;
    }

    public function getRarity() : int {
        return self::RARITY_UNCOMMON;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        if($isAttacker && mt_rand(0, 100) < 8 + $this->getLevel()){
            $player = $ev->getEvent()->getEntity();
            if($player instanceof Living){
                $player->getEffects()->add(new EffectInstance(VanillaEffects::POISON(), 100, 0));
            }
        }
    }
}