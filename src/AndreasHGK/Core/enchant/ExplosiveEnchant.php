<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\world\Explosion;

class ExplosiveEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_CHESTPLATE];
    }

    public function getDescription() : string {
        return "Explode when killed";
    }

    public function getName() : string {
        return "Explosive";
    }

    public function getId() : int {
        return CustomEnchantIds::EXPLOSIVE;
    }

    public function getRarity() : int {
        return self::RARITY_LEGENDARY;
    }

    public function getMaxLevel() : int {
        return 1;
    }

    public function onKill(EntityDamageEvent $ev, Player $attacker, Item $item, bool $isAttacker): void{
        if(!$isAttacker){
            $target = $ev->getEntity();
            $explode = new Explosion($target->getPosition(), 8, $ev->getEntity());
            $explode->explodeB();
        }
    }
}