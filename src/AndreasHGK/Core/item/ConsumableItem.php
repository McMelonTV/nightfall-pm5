<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use pocketmine\item\Item;

class ConsumableItem extends CustomItem {

    protected $name;

    protected $id;

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function getId() : int {
        return $this->id;
    }

    public function onConsume() : void {

    }

    public function __construct(int $id, string $name, Item $item){
        parent::__construct($id, $name, $item);
    }
}