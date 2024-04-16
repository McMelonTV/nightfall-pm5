<?php

declare(strict_types=1);

namespace AndreasHGK\Core\rank;

use pocketmine\utils\TextFormat;

class MineRank {

    private $name;

    private $id;

    private $perms = [];

    private $tag;

    private $price;

    public function getPrice() : int {
        return $this->price;
    }

    public function setPrice(int $price) : void {
        $this->price = $price;
    }

    public function isHigherThan(MineRank $mr) : bool {
        return $mr->getId() < $this->id;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getId() : int {
        return $this->id;
    }

    public function getTag() : string {
        return TextFormat::colorize($this->tag);
    }

    public function setTag(string $tag) : void {
        $this->tag = $tag;
    }

    public function getPerms() : array {
        return $this->perms;
    }

    public function setPerms(array $perms) : void {
        $this->perms = $perms;
    }

    public function __construct(int $id, string $name){
        $this->id = $id;
        $this->name = $name;
        $this->tag = $name;
    }
}