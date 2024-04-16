<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use pocketmine\item\Item;

abstract class TieredItem extends CustomItem {

    public function __construct(int $id, string $name, int $defaultTier){
        parent::__construct($id, $name, $this->getTier($defaultTier));
    }

    abstract public function getTier(int $tier) : Item;

}