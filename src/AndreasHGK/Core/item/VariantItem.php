<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use pocketmine\item\Item;

abstract class VariantItem extends CustomItem {

    public function __construct(int $id, string $name, int $defaultVariant){
        parent::__construct($id, $name, $this->getVariant($defaultVariant));
    }

    abstract public function getVariant(int $variant, int $var1 = 1) : Item;

}