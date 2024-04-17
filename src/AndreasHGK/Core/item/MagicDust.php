<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\VanillaItems;

class MagicDust extends CustomItem {

    public function __construct(){
        $item = VanillaItems::DYE()->setColor(DyeColor::BLUE);
        $item->setCustomName("§r§1Magic Dust");
        EnchantmentUtils::applyGlow($item);
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::MAGICDUST));
        $item->setLore(["§r§7Can be used to forge enchantments."]);
        parent::__construct(self::MAGICDUST, "magic dust", $item);
    }
}