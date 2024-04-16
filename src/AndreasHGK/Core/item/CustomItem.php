<?php

declare(strict_types=1);

namespace AndreasHGK\Core\item;

use pocketmine\item\Item;

class CustomItem {

    public const STARDUST = 1;
    public const MAGICDUST = 2;
    public const OBSIDIANSHARD = 3;
    public const STEELDUST = 4;
    public const TESTPICKAXE = 5;
    public const BASICPICKAXE = 6;
    public const ADVANCEDPICKAXE = 7;
    public const ENCHANTMENTBOOK = 8;
    public const BASICHELMET = 9;
    public const BASICCHESTPLATE = 10;
    public const BASICLEGGINGS = 11;
    public const BASICBOOTS = 12;
    public const BASICSWORD = 13;
    public const GUIDEBOOK = 14;
    public const ADVANCEDSWORD = 15;
    public const ADVANCEDBOOTS = 16;
    public const ADVANCEDLEGGINGS = 17;
    public const ADVANCEDCHESTPLATE = 18;
    public const ADVANCEDHELMET = 19;
    public const CRATEKEY = 20;
    public const HEALAPPLE = 21;
    public const ENCHANTEDHEALAPPLE = 22;
    public const BASICAXE = 23;
    public const ADVANCEDAXE = 24;
    public const BASICSHOVEL = 25;
    public const ADVANCEDSHOVEL = 26;
    public const ELITEPICKAXE = 27;
    public const KOTHCHESTPLATE = 28;
    public const KOTHPICKAXE = 29;

    protected $name;

    protected $id;

    protected $item;

    public function getItem() : Item {
        return clone $this->item;
    }

    public function setItem(Item $item) : void {
        $this->item = $item;
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

    public function __construct(int $id, string $name, Item $item){
        $this->id = $id;
        $this->name = $name;
        $this->item = $item;
    }
}