<?php

declare(strict_types=1);

namespace AndreasHGK\Core\crate;

class Crate {

    private $name;

    private $id;

    /** @var array|CrateItem[] */
    private $items = [];

    private $dropChance = 0;

    public function getDropChance() : int {
        return $this->dropChance;
    }

    public function setDropChance(int $chance) : void {
        $this->dropChance = $chance;
    }

    /**
     * @return array|CrateItem[]
     */
    public function getItems() : array {
        return $this->items;
    }

    public function setItems(array $items) : void {
        $this->items = $items;
    }

    public function getTotalChance() : int {
        $chance = 0;
        foreach($this->items as $item){
            $chance += $item->getChance();
        }

        return $chance;
    }

    public function getRandomItem() : CrateItem {
        $chanceArray = [];
        foreach ($this->items as $item){
            $chanceArray = array_merge($chanceArray, array_fill(0, $item->getChance(), $item));
        }

        $item = $chanceArray[array_rand($chanceArray)];

        return $item;
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function getId() : int {
        return $this->id;
    }

    public function __construct(int $id, string $name, array $items = [], int $dropChance = 0){
        $this->id = $id;
        $this->name = $name;
        $this->dropChance = $dropChance;
        /** @var CrateItem $item */
        foreach($items as $item){
            $this->items[$item->getId()] = $item;
        }
    }
}