<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;

class LifestealEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_SWORD];
    }

    public function getDescription() : string {
        return "Receive health when killing someone";
    }

    public function getName() : string {
        return "Lifesteal";
    }

    public function getId() : int {
        return CustomEnchantIds::LIFESTEAL;
    }

    public function getRarity() : int {
        return self::RARITY_MYTHIC;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    public function onKill(EntityDamageEvent $ev, Player $attacker, Item $item, bool $isAttacker) : void{
        if($isAttacker && $attacker !== $ev->getEntity()){
            $attacker->setHealth(max($attacker->getHealth() + $this->getLevel(), $attacker->getMaxHealth()));
        }
    }
}