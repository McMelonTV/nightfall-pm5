<?php

declare(strict_types=1);

namespace AndreasHGK\Core\forge;

use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\TieredItem;
use AndreasHGK\Core\item\VariantItem;
use AndreasHGK\Core\Price;
use pocketmine\item\Item;

class ForgeItem {

    private $name;

    private $displayTag;

    private $customId;

    /** @var Price */
    private $price;

    public function getName() : string {
        return $this->name;
    }

    public function getPrice() : Price {
        return $this->price;
    }

    public function getCustomId() : string {
        return $this->customId;
    }

    public function getDisplayTag() : string {
        return $this->displayTag;
    }

    public function getItem() : Item {
        $array = explode(":", $this->customId);
        $id = (int)array_shift($array);
        if(!empty($array)){
            $meta = (int)array_shift($array);
        }else{
            $meta = -1;
        }

        $cItem = CustomItemManager::getInstance()->get($id);
        $item = $cItem->getItem();
        if($cItem instanceof TieredItem){
            $item = $meta !== -1 ? $cItem->getTier($meta) : $cItem->getItem();
        }

        if($cItem instanceof VariantItem){
            $item = $meta !== -1 ? $cItem->getVariant($meta) : $cItem->getItem();
        }

        return $item;
    }

    public function __construct(string $name, string $customId, Price $price = null, string $displayTag = null) {
        $this->name = $name;
        $this->customId = $customId;
        $this->displayTag = $displayTag;
        if($price === null){
            $this->price = new Price();
        }else{
            $this->price = $price;
        }

        if($displayTag === null){
            $this->displayTag = $name;
        }
    }
}