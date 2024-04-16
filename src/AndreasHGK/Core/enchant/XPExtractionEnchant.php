<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class XPExtractionEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_PICKAXE];
    }

    public function getDescription() : string {
        return "Get more XP while mining.";
    }

    public function getName() : string {
        return "XP Extraction";
    }

    public function getId() : int {
        return CustomEnchantIds::XPEXTRACTION;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::PICKAXE;
    }

    public function getRarity() : int {
        return self::RARITY_RARE;
    }

    public function getMaxLevel() : int {
        return 5;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $ev->setXPBoost($ev->getXPBoost() + $this->level);
    }
}