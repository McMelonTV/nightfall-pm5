<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\crate\CrateManager;
use AndreasHGK\Core\utils\EnchantmentUtils;
use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;

class CrateKey extends VariantItem {

    public function __construct(){
        parent::__construct(self::CRATEKEY, "cratekey", 10);
    }

    public function getVariant(int $variant, int $var1 = 1): Item{
        $item = $this->getBasicItem();

        $crate = CrateManager::getInstance()->get($variant);
        if($crate === null){
            $crate = CrateManager::getInstance()->get(10);
        }

        ItemUtils::variant($item, $variant.":".$var1);
        $item->setCustomName("§r§b§l".$crate->getName()."§r§7 key");
        $item->setLore(["§r§7Go to §b/crates§r§7 to open the crate.\n§r§7Tap the crate to open it.\n§r§7Sneak + tap the crate to view possible rewards."]);
        $item->getNamedTag()->setInt("cratekey", $crate->getId());
        ItemUtils::customID($item, self::CRATEKEY);
        return $item;
    }

    public function getBasicItem() : Item {
        $item = VanillaBlocks::TRIPWIRE_HOOK()->asItem();
        EnchantmentUtils::applyGlow($item);
        return $item;
    }
}