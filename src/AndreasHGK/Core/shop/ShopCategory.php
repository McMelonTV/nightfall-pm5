<?php

declare(strict_types=1);

namespace AndreasHGK\Core\shop;

class ShopCategory {

    private $name;

    private $itemIcon;

    private $tag;

    /**
     * @var array|ShopItem[]
     */
    private $items = [];

    public function getName() : string {
        return $this->name;
    }

    public function getItemIcon() : string {
        return $this->itemIcon;
    }

    public function getTag() : string {
        return $this->tag;
    }

    public function setTag(string $tag) : void {
        $this->tag = $tag;
    }

    /**
     * @return array|ShopItem[]
     */
    public function getItems() : array {
        return $this->items;
    }

    public function getItem(string $id) : ?ShopItem {
        return $this->items[$id] ?? null;
    }

    public function setItems(array $items) : void {
        $this->items = $items;
    }

    public function __construct(string $name, string $itemIcon, array $items = [], string $tag = null){
        $this->name = $name;
        $this->itemIcon = $itemIcon;
        $this->items = $items;
        $this->tag = $tag ?? "§7".$name;
        foreach($this->items as $item){
            $item->setCategory($this);
        }
    }
}