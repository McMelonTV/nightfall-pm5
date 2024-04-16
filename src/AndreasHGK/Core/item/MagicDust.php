<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class MagicDust extends CustomItem {

    public function __construct(){
        $item = ItemFactory::getInstance()->get(ItemIds::DYE, 18, 1);
        $item->setCustomName("§r§1Magic Dust");
        EnchantmentUtils::applyGlow($item);
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::MAGICDUST));
        $item->setLore(["§r§7Can be used to forge enchantments."]);
        parent::__construct(self::MAGICDUST, "magic dust", $item);
    }
}