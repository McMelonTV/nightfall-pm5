<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;

class StardustExtractionEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_PICKAXE];
    }

    public function getDescription() : string {
        return "Get stardust while mining. You will not get steeldust anymore.";
    }

    public function getName() : string {
        return "Stardust Extraction";
    }

    public function getId() : int {
        return CustomEnchantIds::STARDUST_EXTRACTION;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::PICKAXE;
    }

    public function getRarity() : int {
        return self::RARITY_MYTHIC;
    }

    public function getMaxLevel() : int {
        return 1;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $ev->setMineStardust(true);
    }
}