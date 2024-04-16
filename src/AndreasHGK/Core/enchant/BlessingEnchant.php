<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\entity\Living;
use pocketmine\item\Item;

class BlessingEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_HELMET];
    }

    public function getDescription() : string {
        return "Has a chance to remove all negative effects when hit";
    }

    public function getName() : string {
        return "Blessing";
    }

    public function getId() : int {
        return CustomEnchantIds::BLESSING;
    }

    public function getRarity() : int {
        return self::RARITY_RARE;
    }

    public function getMaxLevel() : int {
        return 6;
    }

    public function onHit(CEAttackEvent $ev, Item $item, bool $isAttacker) : void{
        if(!$isAttacker && mt_rand(0, 100) < 10 + $this->getLevel()*3){
            $player = $ev->getEvent()->getEntity();
            if($player instanceof Living){
                $effects = $player->getEffects();
                foreach($effects->all() as $effectInstance){
                    if($effectInstance->getType()->isBad()) {
                        $effects->remove($effectInstance->getType());
                    }
                }
            }
        }
    }
}