<?php

declare(strict_types=1);

namespace AndreasHGK\Core\tag;

use AndreasHGK\Core\user\User;

class Tag {

    public const COMMON = 1;
    public const UNCOMMON = 2;
    public const RARE = 3;
    public const VERY_RARE = 4;
    public const LEGENDARY = 5;

    private $id;

    private $tag;

    private $rarity;

    private $isPublic = false;

    private $isCrateDrop = true;

    private $receiveOnJoin = false;

    public function getWeight() : int {
        return 6-$this->rarity;
    }

    public function getReceiveOnJoin() : bool{
        return $this->receiveOnJoin;
    }

    public function setReceiveOnJoin(bool $receive) : void {
        $this->receiveOnJoin = $receive;
    }

    public function isCrateDrop() : bool {
        return $this->isCrateDrop;
    }

    public function setCrateDrop(bool $drop) : void {
        $this->isCrateDrop = $drop;
    }

    public function hasPermission(User $user) : bool {
        if($this->isPublic()) return true;
        return array_key_exists($this->getId(), $user->getTags());
    }

    public function getId() : string {
        return $this->id;
    }

    public function getTag() : string {
        return $this->tag;
    }

    public function isPublic() : bool {
        return $this->isPublic;
    }

    public function setPublic(bool $public = true) : void {
        $this->isPublic = $public;
    }

    public function getRarity() : int {
        return $this->rarity;
    }

    public function getRarityName() : string {
        switch ($this->rarity){
            case self::UNCOMMON:
                return "uncommon";
            case self::RARE:
                return "rare";
            case self::VERY_RARE:
                return "very rare";
            case self::LEGENDARY:
                return "legendary";
            default:
                return "common";
        }
    }

    public function __construct(string $id, string $tag, int $rarity, bool $public = false){
        $this->id = $id;
        $this->tag = $tag;
        $this->rarity = $rarity;
        $this->isPublic = $public;
    }
}