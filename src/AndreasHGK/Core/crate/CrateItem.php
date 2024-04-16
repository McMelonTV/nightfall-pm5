<?php

declare(strict_types=1);

namespace AndreasHGK\Core\crate;

use AndreasHGK\Core\item\CustomItemManager;
use AndreasHGK\Core\item\TieredItem;
use AndreasHGK\Core\item\VariantItem;
use pocketmine\item\Item;

class CrateItem {

    private $id;

    private $item;

    private $reward = ["$" => 0, "pp" => "0"];

    private $giveItem;

    private $crateName;

    private $callback;

    private $chance;

    private $sendMessage;

    public function doSendMessage() : bool {
        return $this->sendMessage;
    }

    public function getChance() : int {
        return $this->chance;
    }

    public function getCallback() : ?callable {
        return $this->callback;
    }

    public function setCallback(callable $callback) : void {
        $this->callback = $callback;
    }

    public function getCrateName() : ?string {
        return $this->crateName;
    }

    public function doGiveItem() : bool {
        return $this->giveItem;
    }

    public function getId() : string {
        return $this->id;
    }

    /**
     * @return Item|string
     */
    public function getRealItem() {
        return $this->item;
    }

    /**
     * @return Item
     */
    public function getItem() : Item {
        if($this->item instanceof Item) {
            return $this->item;
        }

        $fullid = $this->item;
        $array = explode(":", $fullid);
        $id = $array[0];
        $count = $array[1] ?? 1;
        $meta = $array[2] ?? 1;
        $meta2 = $array[3] ?? 1;
        $cItem = CustomItemManager::getInstance()->get((int)$id);
        if($cItem instanceof TieredItem){
            $item = $cItem->getTier((int)$meta);
        }elseif($cItem instanceof VariantItem){
            $item = $cItem->getVariant((int)$meta, (int)$meta2);
        }else{
            $item = clone $cItem->getItem();
        }

        $item->setCount((int)$count);
        return $item;
    }

    public function setItem($item) : void {
        $this->item = $item;
    }

    public function getReward() : array {
        return $this->reward;
    }

    public function setReward(array $reward) : void {
        $this->reward = $reward;
    }

    public function getRewardDollars() : int {
        return $this->reward["$"] ?? 0;
    }

    public function getRewardPrestige() : int {
        return $this->reward["pp"] ?? 0;
    }

    public function setRewardDollars(int $price) : void {
        $this->reward["$"] = $price;
    }

    public function setRewardPrestige(int $price) : void {
        $this->reward["pp"] = $price;
    }

    public function __construct(string $id, $item, ?string $crateName = null, int $chance = 1000, int $dollar = 0, int $prestige = 0, bool $giveItem = true, callable $callback = null, bool $sendMessage = true){
        $this->chance = $chance;
        $this->id = $id;
        $this->item = $item;
        $this->setRewardDollars($dollar);
        $this->setRewardPrestige($prestige);
        $this->giveItem = $giveItem;
        $this->crateName = $crateName;
        $this->callback = $callback;
        $this->sendMessage = $sendMessage;
        if($item instanceof Item && !$item->hasCustomName()){
            $item->setCustomName("§r§b".$crateName);
        }
    }
}