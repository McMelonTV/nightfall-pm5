<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use AndreasHGK\Core\utils\ItemUtils;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class HealApple extends CustomItem {

    public function __construct(){
        $item = ItemFactory::getInstance()->get(ItemIds::APPLE, 18, 1);
        $item->setCustomName("§r§aHealth Apple");
        $item->setNamedTag($item->getNamedTag()->setInt("customitem", self::HEALAPPLE));
        $item = ItemUtils::description($item, "§r§7Get §a+2 hp §r§7when consumed");
        parent::__construct(self::HEALAPPLE, "healapple", $item);
    }

}