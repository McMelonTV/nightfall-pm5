<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\EnchantmentUtils;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\VanillaItems;

class ObsidianShard extends CustomItem implements RepairResource {

    public function getRepairValue(): int{
        return 400;
    }

    public function __construct(){
        $item = VanillaItems::DYE()->setColor(DyeColor::BLACK);
        $item->setCustomName("§r§0Obsidian Shard");
        EnchantmentUtils::applyGlow($item);
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::OBSIDIANSHARD));
        $item->setLore(["§r§7Can be used to more efficiently repair tools and armor."]);
        parent::__construct(self::OBSIDIANSHARD, "obsidianshard", $item);
    }
}