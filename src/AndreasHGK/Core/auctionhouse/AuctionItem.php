<?php

declare(strict_types=1);

namespace AndreasHGK\Core\auctionhouse;

use pocketmine\item\Item;

class AuctionItem {

    private $id;

    private $item;

    private $seller;

    private $sellTime;

    private $price;

    public function getPrice() : int{
        return $this->price;
    }

    public function getFullId() : string{
        return $this->seller.":".$this->id;
    }

    public function getItem() : Item{
        return $this->item;
    }

    public function getSeller() : string{
        return $this->seller;
    }

    public function getSellTime() : int{
        return $this->sellTime;
    }

    public function getId() : string{
        return $this->id;
    }

    public function __construct(string $id, Item $item, string $seller, int $sellTime, int $price){
        $this->id = $id;
        $this->item = $item;
        $this->seller = $seller;
        $this->sellTime = $sellTime;
        $this->price = $price;
    }
}