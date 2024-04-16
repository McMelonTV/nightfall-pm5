<?php

declare(strict_types=1);

namespace AndreasHGK\Core\shop;

use pocketmine\item\Item;

class ShopItem {

    private $id;

    private $item;

    private $price = ["$" => 0, "pp" => "0"];

    private $giveItem = true;

    private $desc = "";

    private $shopName = "";

    private $callback = null;

    /** @var ShopCategory */
    private $category = null;

    private $oneTime = false;

    public function isOneTime() : bool {
        return $this->oneTime;
    }

    public function setOneTime(bool $oneTime) : void {
        $this->oneTime = $oneTime;
    }

    public function fullId() : string {
        return $this->category->getName()."//".$this->getId();
    }

    public function setCategory(ShopCategory $category) : void {
        $this->category = $category;
    }

    public function getCategory() : ShopCategory {
        return $this->category;
    }

    public function getCallback() : ?callable {
        return $this->callback;
    }

    public function setCallback(callable $callback) : void {
        $this->callback = $callback;
    }

    public function getShopName() : ?string {
        return $this->shopName;
    }

    public function getDesc() : string {
        return $this->desc;
    }

    public function setDesc(string $desc) : void {
        $this->desc = $desc;
    }

    public function doGiveItem() : bool {
        return $this->giveItem;
    }

    public function getId() : string {
        return $this->id;
    }

    public function getItem() : Item {
        return $this->item;
    }

    public function setItem(Item $item) : void {
        $this->item = $item;
    }

    public function getPrice() : array {
        return $this->price;
    }

    public function setPrice(array $price) : void {
        $this->price = $price;
    }

    public function getPriceDollars() : int {
        return $this->price["$"] ?? 0;
    }

    public function getPricePrestige() : int {
        return $this->price["pp"] ?? 0;
    }

    public function setPriceDollars(int $price) : void {
        $this->price["$"] = $price;
    }

    public function setPricePrestige(int $price) : void {
        $this->price["pp"] = $price;
    }

    public function __construct(string $id, Item $item, string $desc = "", int $dollar = 0, int $prestige = 0, bool $giveItem = true, ?string $shopName = null, bool $oneTime = false, callable $callback = null){
        $this->id = $id;
        $this->item = $item;
        $this->desc = $desc;
        $this->setPriceDollars($dollar);
        $this->setPricePrestige($prestige);
        $this->giveItem = $giveItem;
        $this->shopName = $shopName;
        $this->oneTime = $oneTime;
        if(isset($shopName)) {
            $item->setCustomName("§r§b".$shopName);
        }

        $this->callback = $callback;
    }
}