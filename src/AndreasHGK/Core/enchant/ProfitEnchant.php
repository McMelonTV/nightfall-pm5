<?php

declare(strict_types=1);

namespace AndreasHGK\Core\enchant;

use pocketmine\item\enchantment\ItemFlags;

class ProfitEnchant extends CustomEnchant {

    public function getCompatible() : array {
        return [self::TYPE_PICKAXE];
    }

    public function getDescription() : string {
        return "Sell your mined resources at a higher price.";
    }

    public function getName() : string {
        return "Profit";
    }

    public function getId() : int {
        return CustomEnchantIds::PROFIT;
    }

    public function getCompatibleSlots() : int {
        return ItemFlags::PICKAXE | ItemFlags::SHOVEL;
    }

    public function getRarity() : int {
        return self::RARITY_VERY_RARE;
    }

    public function getMaxLevel() : int {
        return 10;
    }

    //events

    public function onMine(CEMineEvent $ev) : void{
        $profit = $ev->getPriceModifier() + (0.2 * $this->level);
        $ev->setPriceModifier($profit);
    }
}