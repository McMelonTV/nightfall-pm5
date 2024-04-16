<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\ItemFlags;

class FusionEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_PICKAXE];
    }

    public function getDescription() : string {
        return "Gives the tool a chance to transform a resource into a higher value resource.";
    }

    public function getName() : string {
        return "Fusion";
    }

    public function getId() : int {
        return CustomEnchantIds::FUSION;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::PICKAXE;
    }

    public function getRarity() : int {
        return self::RARITY_UNCOMMON;
    }

    public function getMaxLevel() : int{
        return 5;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $rand = mt_rand(0, 100);
        if($rand <= (20*$this->level)){
            $ev->setFusion(true);
        }
    }
}