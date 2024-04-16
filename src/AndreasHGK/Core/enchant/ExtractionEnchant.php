<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class ExtractionEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_PICKAXE];
    }

    public function getDescription() : string {
        return "Get more resources while mining.";
    }

    public function getName() : string {
        return "Extraction";
    }

    public function getId() : int {
        return CustomEnchantIds::EXTRACTION;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::PICKAXE;
    }

    public function getRarity() : int {
        return self::RARITY_MYTHIC;
    }

    public function getMaxLevel() : int {
        return 8;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $ev->setResourceBoost($ev->getResourceBoost() + 10*$this->level);
    }
}